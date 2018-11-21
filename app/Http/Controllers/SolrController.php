<?php
require_once( ROOT_PATH.'/includes/Solr/Service.php');
class SolrSearch{
    const SOLR_HOST = '172.18.107.96';
    const SOLR_PORT = '8888';
    const SOLR_URL = 'solr';

    /**
     * php通过solr文件包连接服务器端
     */
    public static function solr_server(){
        $solrSearcher = new Apache_Solr_Service( self::SOLR_HOST, self::SOLR_PORT, self::SOLR_URL );
        if ( ! $solrSearcher->ping() ) {
            echo 'Solr service not responding.';
            exit;
        }
        return $solrSearcher;
    }

    /**
     * php通过solr扩展连接服务器端
     */
    public static function extention_solr_server()
    {
        $extention_solrconfig=array('hostname' => self::SOLR_HOST,'port'=>self::SOLR_PORT);
        try {
            $solrSearcher = new SolrClient($extention_solrconfig);
            $solrSearcher->ping();  //需要，不调用ping()的时候如果服务器连接不上不会抛异常
        } catch (Exception $e) {
            echo "Solr extention service connect fail.";
            exit;
        }
        return $solrSearcher;
    }

    /**
     * php文件包方式查询方法
     */
    public static function solr_query($query,$pageinfo=array('offset'=>0,'limit'=>600),
                                      $params=array('fl'=>'','fq'=>array())){
        $solrSearcher=self::solr_server();

        $params['fl']=$params['fl']?$params['fl'].",score":"*,score";
        $response = $solrSearcher->search( $query , $pageinfo['offset'], $pageinfo['limit'] ,$params);

        $result=array();
        $found_num=0;
        $http_status=$response->getHttpStatus();

        if ( $http_status == 200 ) {
            $response_result=$response->response;
            $found_num=$response_result->numFound;

            if ( $found_num > 0 ) {
                $search_docs=$response_result->docs;
                $highlighting=$response->highlighting;
                foreach ($search_docs as $s_k=>$s_v){
                    $lists=get_object_vars($s_v->getIterator());
                    $lists['goods_color_name']=empty($highlighting->$lists['goods_id']->goods_name[0]) ? $lists['goods_name'] : $highlighting->$lists['goods_id']->goods_name[0];
                    $result['lists'][]=$lists;
                }
            }
            $result['total']=$found_num;
            $result['goods_status']=array();
            $result['total_found']=$found_num;
        }
        else {
            echo $response->getHttpStatusMessage();
        }

        return $result;
    }

    /**
     * 获取关键字分词后形成的词
     */
    public static function getWords($keyword)
    {
        $keyword = trim($keyword);
        $words = array();
        if ($keyword)
        {
            $wordsInfo = self::getWordsInfo($keyword);
            if (is_array($wordsInfo) && !empty($wordsInfo))
            {
                foreach ($wordsInfo as $val)
                {
                    $words[] = $val['text'];
                }
            }
        }
        return $words;
    }

    /**
     * 获取关键字分词后的信息
     */
    public static function getWordsInfo($keyword)
    {
        $keyword = urlencode($keyword);

        // 拼接 调用分词接口的url
        $url = "http://172.18.107.96:8888/solr/collection1/analysis/field?wt=json&analysis.showmatch=true&analysis.fieldvalue=".$keyword."&analysis.fieldname=MaxWord&_=".time();
        $i = 0;               // 记录查询失败的次数
        $code = 0;            // 记录查询状态
        $responseStr = "";    // 接口返回json字符串
        while($code == 0 && $i < 5)  //查询失败重试
        {
            $ch=curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_HEADER,0);
            curl_setopt($ch,CURLOPT_TIMEOUT,10);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

            $responseStr = curl_exec($ch);
            $code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
            $i += $code ? 0 : 1;    //失败则查询次数加1
            curl_close($ch);
        }

        // 解析接口返回的字符串 获取分词信息以数组形式保存
        if ($responseStr)
        {
            $responseArr = json_decode($responseStr,true);
            if (isset($responseArr['analysis']['field_names']['MaxWord']['index']))
            {
                return $responseArr['analysis']['field_names']['MaxWord']['index'][1];
            }
        }
        return array();
    }

    /**
     * 将收集到查询条件转换成solr要用到的信息 进行solr搜索
     */
    public static function find($conditions = array())
    {
        // 定义存储 solr应用中q查询操作信息 $q = "goods_name:红色帽子 OR price:30";(对象包含关键字，一二级分类，属性)
        $q = "";
        if (empty($_GET['keyword']))
        {
            $q .= "goods_name:*";
        }
        else
        {
            $q .= "goods_name:".$_GET['keyword'];
        }

        // 拼接分类条件信息
        if (!empty($conditions['select']))
        {
            if ($conditions['select'] == 1)
            {
                $q .= " AND cate_id_1:".$conditions['filter']['cate_id_1'][0];
            }
            else if ($conditions['select'] == 2)
            {
                $q .= " AND cate_id_2:".$conditions['filter']['cate_id_2'][0];
            }
        }

        // 拼接属性条件信息
        if (!empty($_GET['g']))    // [g] => 2966_15449-2961_3007
        {
            $attr_id_arr = explode('-', $_GET['g']);

            foreach ($attr_id_arr as $val)
            {
                $arr = explode('_', $val);
                $q .= " AND attr_id:*".$arr[1]."*";
            }
        }

        // 判断扩展方式是否可用，决定调用的方式
        if (class_exists('SolrClient'))    // 通过solr的php扩展方式进行solr的连接处理
        {
            $client = self::extention_solr_server();
            $query = new SolrQuery();
            // 设置sorl查询的q信息
            //$query->setQuery('goods_name:红色帽子');
            $query->setQuery($q);
            // 设置获取结果的起始位置
            $query->setStart(0);
            // 设置获取结果的记录数
            $query->setRows(600);        // 类似分页的取出记录条数
            // 设置排序信息SolrQuery::ORDER_ASC ,SolrQuery::ORDER_DESC
            //$query->addSortField("goods_id",SolrQuery::ORDER_DESC);
            // 设置sorl查询的fq信息
            $fqInfo = self::getFqInfo($conditions,$query);
            if (!empty($fqInfo))
            {
                $query = $fqInfo;
            }

            $queryResponse = $client->query($query);
            $response = $queryResponse->getResponse();
            if ( $response->responseHeader->status == 0 ) {
                $response_result=$response->response;
                $found_num=$response_result->numFound;

                if ( $found_num > 0 ) {
                    $search_docs=$response_result->docs;
                    $highlighting=$response->highlighting;

                    foreach ($search_docs as $s_k=>$s_v){
                        // $lists=get_object_vars($s_v->getIterator());
                        $lists=json_decode(json_encode($s_v),true);
                        $lists['goods_color_name']=empty($highlighting->$lists['goods_id']->goods_name[0]) ? $lists['goods_name'] : $highlighting->$lists['goods_id']->goods_name[0];
                        $result['lists'][]=$lists;
                    }
                }
                $result['total']=$found_num;
                $result['goods_status']=array();
                $result['total_found']=$found_num;
            }
            else {
                echo $response->getHttpStatusMessage();
            }
            $data = $result;
        }
        else    // 通过solr的php文件包方式进行solr的连接处理
        {
            // 定义存储 solr应用中排序sort,过滤查询fq,返回结果字段fl等信息 的数组
            $params = array();
            $params['fl'] = "";
            //$params['sort'] = $conditions['order'];  //权重分字段没有，暂时注释
            $params['sort'] = "goods_id desc";

            // 定义高亮显示参数(有关键字时将关键字高亮)
            if (!empty($_GET['keyword']))
            {
                $params['hl'] = "true";
                $params['hl.fl'] = "goods_name";
                $params['hl.simple.pre'] = "<em class=\"highlight-keyword\">";
                $params['hl.simple.post'] = "</em>";
            }

            // 定义存储fq过滤信息数组，每个字段的条件设置作为数组$fq一个元素(包含)
            $fq = self::getFqInfo($conditions);
            if (!empty($fq))
            {
                $params['fq'] = $fq;
            }

            // 用拼接好的参数调用solr查询
            $data = self::solr_query($q,array('offset'=>0,'limit'=>600),$params);
        }

        // clear参数为7打印语句
        if ($_GET['clear'] == 7)
        {
            echo "<pre>";print_r($data);exit;
        }

        // 将关键字信息存入data数据中
        $data['words']=self::getWords($_GET['keyword']);
        return $data;
    }

    /**
     *
     * 依照前台处理后的条件数据转换成solr查询应用的过滤fq信息
     * $query object 区别当前是以php文件包的方式还是扩展的方式调用solr(默认是php文件包方式，$query不为null则是扩展方式)
     * @param array $conditions
     */
    private static function getFqInfo($conditions,$query=null)
    {
        // 定义前台传递参数与solr应用参数之间的key对应关系(其中数组key为前台key，数组val为solr应用key)
        $solr_key=array(
            'price'=>'price',                                // 价格
            'return_goods_status'=>'return_goods_status',    // 48小时包退包换
            'if_free_shipping'=>'if_free_shipping',          // 免运费字段
            'if_new_goods'=>'if_new_goods',                  // 新品
            'real_shot'=>'real_shot',                        // 实拍
            'is_enterprise'=>'is_enterprise',                // 企业身份认证
            'is_entity'=>'is_entity',                        // 实体认证
            'is_behalfof'=>'is_behalfof',                    // 一件代发
            'is_specialmem_status'=>'is_specialmem_status',  // 免费看样
            'is_promise'=>'is_promise',                      // 店铺等级
            //'minbuy'=>'minbuy',                              // 起批数量
            //'minbuyprice'=>'minbuyprice',                    // 起批价格
        );

        // 定义通过solr的php文件包形式调用solr服务器时fq信息存储数组
        $fq = array();

        // 当前台页面传递过来的参数中包含过滤条件时，依照不同的调用方式解析成solr应用的过滤fq信息
        if (!empty($conditions['filter']))
        {
            foreach ($conditions['filter'] as $key=>$val)
            {
                $fq_info_str = "";
                if (array_key_exists($key, $solr_key))
                {
                    if ($key == "price")
                    {
                        $minPrice = strval(empty($val['min']) ? 0 : $val['min']);
                        $maxPrice = strval(empty($val['max']) ? "*" : $val['max']);
                        $fq_info_str = "price:[".$minPrice." TO ".$maxPrice."]";
                    }
                    else
                    {
                        if (is_array($val))
                        {
                            $fq_info_str = $solr_key[$key].":".$val[0];
                        }
                        else
                        {
                            $fq_info_str = $solr_key.":".$val[0];
                        }
                    }
                }

                /* $query不为null时，此时通过solr的php扩展方式调用solr服务器，fq信息通过$query->addFilterQuery("price:30");添加
                 * $query为null时，此时通过solr的php文件包调用solr服务器，fq信息通过$fq[] = "price:30";添加
                 */
                if ($fq_info_str != "")
                {
                    if ($query != null)
                    {
                        $query->addFilterQuery($fq_info_str);
                    }
                    else
                    {
                        $fq[] = $fq_info_str;
                    }
                }
            }

            // 依照调用方式的不同返回不同的值
            if ($query != null)
            {
                return $query;
            }
            else
            {
                return $fq;
            }
        }
        else
        {
            return ;
        }
    }
}
