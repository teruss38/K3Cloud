<?php

//namespace service\k3cloud\exception;

/**
 * K3cloud异常处理类
 * @author Hogen
 */
class K3cloudException extends \Exception
{
    /** @var mixed */
    private $_message = null;
    /** @var string */
    private $_requestAction = null;
    /** @var string */
    private $_errorCode = null;
    /** @var string */
    private $_errorType = null;
    /** @var string */
    private $_org = null;

    /**
     * 构造方法
     * @param array $errorInfo
     */
    public function __construct(array $errorInfo = array())
    {
        if (array_key_exists("Exception", $errorInfo)) {
            $exception = $errorInfo["Exception"];
            if ($exception instanceof K3cloudException) {
                $this->_requestAction = $exception->getRequestAction();
                $this->_errorCode = $exception->getErrorCode();
                $this->_errorType = $exception->getErrorType();
                $this->_org = $exception->getORG();
            }
        } else {
            $this->_requestAction = $this->arrVal($errorInfo, "requestAction");
            $this->_errorCode = $this->arrVal($errorInfo, "errorCode");
            $this->_errorType = $this->arrVal($errorInfo, "errorType");
            $this->_org = $this->arrVal($errorInfo, "ORG");
        }

//        if (is_array($errorInfo["message"])) {
//            $this->_message = json_encode($errorInfo["message"], JSON_UNESCAPED_UNICODE);
//        } else {
        $this->_message = $errorInfo["message"];
//        }
//        parent::__construct($this->_message, $this->_errorCode);
    }

    /**
     * 从数组里取值
     * @param array $arr
     * @param string $key
     * @return unknown|NULL
     */
    private function arrVal($arr, $key)
    {
        if (array_key_exists($key, $arr)) {
            return $arr[$key];
        } else {
            return null;
        }
    }

    /**
     * 获取错误代码
     * @return Ambigous <unknown, NULL, array>
     */
    public function getErrorCode()
    {
        return $this->_errorCode;
    }

    /**
     * 获取错误类型
     * @return Ambigous <unknown, NULL, array>
     */
    public function getErrorType()
    {
        return $this->_errorType;
    }

    /**
     * 获取错误信息
     * @return Ambigous <unknown, NULL, array>
     */
    public function getErrorMessage()
    {
        return $this->_message;
    }

    /**
     * 获取请求接口名
     * @return Ambigous <unknown, NULL, array>
     */
    public function getRequestAction()
    {
        return $this->_requestAction;
    }

    /**
     * 获取返回的原始数据
     * @return Ambigous <unknown, NULL, array>
     */
    public function getORG()
    {
        return $this->_org;
    }

}
