<?php
//namespace ;

/**
 * K3Cloud请求类
 * @author hogen
 */
class RequestService
{
    //缓存
    protected $_cookieJar = '';
    //请求接口名
    protected $_requestAction = "";

    /**
     * response success格式
     * @param array $data
     * @param string $message
     * @param int $code
     * @return array
     */
    public function success($data = [], $message = "success", $code = 200)
    {
        return [
            'ack' => true,
            "code" => $code,
            "meesage" => $message,
            'data' => $data
        ];
    }

    /**
     * response error格式
     * @param array $data
     * @param string $message
     * @param int $code
     * @return array
     */
    public function error($message = "error", $code = 500)
    {
        return [
            "code" => $code,
            "meesage" => $message,
        ];
    }

    /**
     * 调取接口
     * @param string $url
     * @param string $postContent
     * @param bool $isLogin
     * @return bool|string
     */
    protected function _curl(string $url, string $postContent, bool $isLogin = FALSE)
    {
        $ch = curl_init($url);

        $thisHeader = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($postContent)
        );

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $thisHeader);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postContent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($isLogin) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->_cookieJar);
        } else {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->_cookieJar);
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        //最多循环三次
        $requestCount = 1;
        $return['requestAction'] = $this->_requestAction;
        $return['org'] = $postContent;
        while ($requestCount <= 3) {
            //执行请求
            $return['result'] = curl_exec($ch);

            //curl是否发生错误
            if ($errNo = curl_errno($ch)) {
                $return['ack'] = false;
                $return['errorType'] = 'Internalc Error';
                $return['errorCode'] = 500;
                $return['message'] = 'K3cloud CurlRequestError,ErrNo:' . $errNo . ',Error:' . curl_error($ch);
            } else {
                $return['ack'] = true;
                $return['message'] = 'success';
                break;
            }
            //请求次数累加
            $requestCount++;
        }
        curl_close($ch);

        if (!$return['ack']) {
            throw new \K3cloudException($return);
        }

        return $return;
    }


//    /**
//     * curl请求
//     * @param string $type
//     * @param string $url
//     * @param string $postContent json格式的数据
//     * @param bool $isLogin
//     * @param array $option
//     * @return array
//     * @example $option=array(
//     *         'TimeOut'=>120  //超时时间
//     * );
//     */
//    protected function _curl(string $type = 'GET', string $url, string $postContent = '', bool $isLogin = FALSE, $option = array())
//    {
//        $return = array('ack' => 0, 'message' => '', 'httpStatu' => '', 'data' => array());
//
//        $TimeOut = isset($option['TimeOut']) ? $option['TimeOut'] : 120;
//
//        $ch = curl_init();//初始化资源句柄
//
//        $thisHeader = array(
//            'Content-Type: application/json',
//            'Content-Length: ' . strlen($postContent)
//        );
//
//        curl_setopt($ch, CURLOPT_URL, $url);//设置请求地址
////
//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);//设置http操作类型
////
////        curl_setopt($ch, CURLOPT_VERBOSE, false);//启用时会汇报所有的信息，存放在STDERR或指定的CURLOPT_STDERR中
////
////        curl_setopt($ch, CURLOPT_HEADER, false);//请求头是否包含在响应中
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $thisHeader);   //设置http头
////
////        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);//是否跟随重定向
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//https请求不验证证书
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//https请求不验证hosts
//
////        curl_setopt($ch, CURLOPT_HEADER, true);//返回响应头
//
//        if ($postContent != '') {
////            curl_setopt($ch, CURLOPT_POST, true);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, $postContent);//设置请求数据
//        }
//
//        if ($isLogin) {
//            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->_cookieJar);
//        } else {
//            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->_cookieJar);
//        }
//
//        curl_setopt($ch, CURLOPT_TIMEOUT, $TimeOut);//设置超时时间
//
//        //最多循环三次
//        $requestCount = 1;
//        while ($requestCount <= 3) {
//            //执行请求
////            printLog("K3cloudApi Curl进行第 {$requestCount} 次请求开始...");
//            $data = curl_exec($ch);
//
//            //获取curl请求信息
//            $curlInfo = curl_getinfo($ch);
//
//            $return["httpStatu"] = $curlInfo['http_code'];
//            $this->errorInfo['HttpStatu'] = $curlInfo['http_code'];
//
//            //curl是否发生错误
//            if ($errNo = curl_errno($ch)) {
//                $errMsg = curl_error($ch);
////                printLog("K3cloudApi 第 {$requestCount} 次请求失败,ErrNo:{$errNo},Error:{$errMsg}");
//                $return['message'] = 'K3cloud CurlRequestError,ErrNo:' . $errNo . ',Error:' . $errMsg;
//            } else {
////                printLog("K3cloudApi 第 {$requestCount} 次请求成功!");
//                $return['message'] = '';
//                break;
//            }
//            //请求次数累加
//            $requestCount++;
//        }
//
//        //关闭资源curl句柄
//        curl_close($ch);
//        //没有错误，curl请求成功
//        if ($return["message"] == '') {
//            $return["ack"] = 1;
//            $return["message"] = 'success';
//        }
//        $return["data"] = $data;
//
//        return $return;
//    }
}