<?php

require_once "./trait/JdBuild.php";

class JdService
{
    use JdBuild;

    //API文档
    const LOGIN_API = 'Kingdee.BOS.WebApi.ServicesStub.AuthService.ValidateUser.common.kdsvc';
    const SAVE_API = 'Kingdee.BOS.WebApi.ServicesStub.DynamicFormService.save.common.kdsvc';
    const BATCHSAVE_API = 'Kingdee.BOS.WebApi.ServicesStub.DynamicFormService.batchSave.common.kdsvc';
    const VIEW_API = 'Kingdee.BOS.WebApi.ServicesStub.DynamicFormService.view.common.kdsvc';
    const DRAFT_API = 'Kingdee.BOS.WebApi.ServicesStub.DynamicFormService.draft.common.kdsvc';
    const DELETE_API = 'Kingdee.BOS.WebApi.ServicesStub.DynamicFormService.delete.common.kdsvc';
    const GETBILL_API = 'Kingdee.BOS.WebApi.ServicesStub.DynamicFormService.ExecuteBillQuery.common.kdsvc';
    const QUERYINFO_API = 'Kingdee.BOS.WebApi.ServicesStub.DynamicFormService.QueryBusinessInfo.common.kdsvc';
    // 金蝶域名或者IP地址;/K3Cloud/
    public $cloudUrl = '';
    // cookieJar
    public $cookieJar = '';
    //登陆数据
    public $loginData = [];
    //语言ID,中文2052,繁体3076，英文1033
    public $LCID = 2052;

    /**
     * 默认每次调用接口前先login，因为不知道login的时效性，没有做单例模式。
     * @param string $cloudUrl URL
     * @param string $acctID 帐套ID
     * @param string $username 用户名
     * @param string $password 密码
     * @param int $LCID 语言ID
     */
    public function __construct(string $cloudUrl, string $acctID, string $username, string $password, int $LCID = 2052)
    {
        $this->cloudUrl = rtrim($cloudUrl, "/") . "/";
        $this->LCID = $LCID;
        $this->loginData = [$acctID, $username, $password, $this->LCID];
        $this->login();
    }

    /**
     * 登陆
     * @return array
     */
    public function login()
    {
        $this->cookieJar = tempnam('./tmp', 'CloudSession'); //保存登录后的session
        $postContent = $this->_createPostData($this->loginData);
        $url = $this->cloudUrl . self::LOGIN_API;
        $res = json_decode($this->_curl($url, $postContent, TRUE), true);
        if ($res['LoginResultType'] != 1) {
            return $res;
        }
        return $res;
    }

    /**
     * 查看
     * @return array|mixed
     */
    public function view()
    {
        $defaultData = [
            "CreateOrgId" => 0,
            "Number" => "",
            "Id" => "",
        ];
        $res = $this->_handle(self::VIEW_API, $defaultData);
        if ($res['Result']['ResponseStatus'] == null) {
            return $res['Result']['Result'];
        } else {
            return [
                'code' => $res['Result']['ResponseStatus']['ErrorCode'],
                'message' => $res['Result']['ResponseStatus']['Errors']
            ];
        }
    }

    /**
     * 保存
     * @return array|mixed
     */
    public function save()
    {
        $defaultData = [
            "Creator" => "",
            "NeedUpDateFields" => [],
            "NeedReturnFields" => [],
            "IsDeleteEntry" => "true",
            "SubSystemId" => "",
            "IsVerifyBaseDataField" => "false",
            "IsEntryBatchFill" => "true",
            "ValidateFlag" => "true",
            "NumberSearch" => "true",
            "InterationFlags" => "",
            "IsAutoSubmitAndAudit" => "false",
        ];
        $this->_nowAction = __FUNCTION__;
        $res = $this->_handle(self::SAVE_API, $defaultData);
        if ($res['Result']['ResponseStatus']['IsSuccess'] == false) {
            return [
                'code' => $res['Result']['ResponseStatus']['ErrorCode'],
                'message' => $res['Result']['ResponseStatus']['Errors']
            ];
        }
        return $res['Result']['ResponseStatus']['SuccessEntitys'];
    }

    /**
     * 批量保存
     * @return array|mixed
     */
    public function batchSave()
    {
        $defaultData = [
            "Creator" => "",
            "NeedUpDateFields" => [],
            "NeedReturnFields" => [],
            "IsDeleteEntry" => "true",
            "SubSystemId" => "",
            "IsVerifyBaseDataField" => "false",
            "IsEntryBatchFill" => "true",
            "ValidateFlag" => "true",
            "NumberSearch" => "true",
            "InterationFlags" => "",
            "IsAutoSubmitAndAudit" => "false",
        ];
        $this->_nowAction = __FUNCTION__;
        $res = $this->_handle(self::BATCHSAVE_API, $defaultData);
        if ($res['Result']['ResponseStatus']['IsSuccess'] == false) {
            return [
                'code' => $res['Result']['ResponseStatus']['ErrorCode'],
                'message' => $res['Result']['ResponseStatus']['Errors']
            ];
        }
        return $res['Result']['ResponseStatus']['SuccessEntitys'];
    }

    /**
     * 删除
     * @return array|bool
     */
    public function delete()
    {
        $defaultData = [
            "CreateOrgId" => 0,
            "Numbers" => [],
            "Ids" => "",
        ];
        $this->_nowAction = __FUNCTION__;
        $res = $this->_handle(self::DELETE_API, $defaultData);
        if ($res['Result']['ResponseStatus']['IsSuccess'] == false) {
            return [
                'code' => $res['Result']['ResponseStatus']['ErrorCode'],
                'message' => $res['Result']['ResponseStatus']['Errors']
            ];
        }
        return true;
    }

    /**
     * 单据查询
     * @return array
     */
    public function getBill(): array
    {
        $defaultData = [
            'FormId' => $this->_formID,
            "FieldKeys" => "",     // 获取字段参数，内码，供应商id，物料id,物料编码，物料名称 ex:FID,FBaseQty,FMaterialId
            "FilterString" => "", // 过滤条件 ex:"FMaterialId.FNumber='HG_TEST'"
            "OrderString" => "", // 排序条件 ex:FID ASC
            "TopRowCount" => 0, // 最多允许查询的数量，0或者不要此属性表示不限制
            "StartRow" => 0,   // 分页取数开始行索引，从0开始，例如每页10行数据，第2页开始是10，第3页开始是20
            "Limit" => $this->_limit,    // 分页取数每页允许获取的数据，最大不能超过200
        ];
        $fields = $this->_fields;
        $this->_nowAction = __FUNCTION__;
        $res = $this->_handle(self::GETBILL_API, $defaultData);
        if (isset($res[0][0]['Result']['ResponseStatus']['IsSuccess'])) {
            $res = $res[0][0];
            return [
                'code' => $res['Result']['ResponseStatus']['ErrorCode'],
                'message' => $res['Result']['ResponseStatus']['Errors']
            ];
        }
        //整理格式
        $newRes = [];
        $fields = explode(",", $fields);
        foreach ($res as $k => $each) {
            foreach ($each as $i => $v) {
                $field = $fields[$i];
                $newRes[$k][$field] = $v;
            }
        }
        return $newRes;
    }

    /**
     * TODO！先不开发 暂存
     * @return array
     */
    public function draft()
    {
        $defalutData = [];
        $this->_nowAction = __FUNCTION__;
        $res = $this->_handle(self::DRAFT_API, $defalutData);
        if ($res['Result']['ResponseStatus']['IsSuccess'] == false) {
            return [
                'code' => $res['Result']['ResponseStatus']['ErrorCode'],
                'message' => $res['Result']['ResponseStatus']['Errors']
            ];
        }
        return $res;
    }

    /**
     * TODO！跑不通 元数据查询
     * @return array
     */
    public function queryInfo()
    {
        $defaultData = [];
        $this->_nowAction = __FUNCTION__;
        $res = $this->_handle(self::QUERYINFO_API, $defaultData);
        if ($res['Result']['ResponseStatus']['IsSuccess'] == false) {
            return [
                'code' => $res['Result']['ResponseStatus']['ErrorCode'],
                'message' => $res['Result']['ResponseStatus']['Errors']
            ];
        }
        return $res;
    }

    /**
     * 处理请求
     * @param string $api
     * @param array $defalutData
     * @return mixed
     */
    private function _handle(string $api, array $defalutData = [])
    {
        $data = $this->_formatData($defalutData);
        $postData = $this->_createPostData($data);
        $url = $this->cloudUrl . $api;
        //还原初始化属性
        $this->_initData();
        return json_decode($this->_curl($url, $postData), true);
    }

    /**
     * 整理数据
     * @param $data
     * @return array
     */
    private function _formatData($data): array
    {
        //替换data属性
        if (!empty($this->_data)) {
            foreach ($this->_data as $k => $v) {
                $data[ucfirst($k)] = $v;
            }
        }
        //替换Model属性
        if (!empty($this->_model)) {
            $data["Model"] = $this->_model;
        }

        //查单据时,强替换属性
        if ($this->_nowAction == "getBill") {
            if ($this->_formID != "") {
                $data["FormId"] = $this->_formID;
                $this->_formID = "";
            }
            if ($this->_fields != "") {
                $data["FieldKeys"] = $this->_fields;
            }
            if ($this->_limit > 0) {
                $data["Limit"] = $this->_limit;
            }
        }
        //整理格式
        if ($this->_formID == "") {
            //有顺序规定，不能调转
            $postData = [
                $data
            ];
        } else {
            $postData = [
                $this->_formID,
                $data
            ];
        }
        return $postData;
    }

    /**
     * 还原初始化属性
     * 因为不知道login的时效性，没有做单例模式，还原属性避免一个实例复用导致的数据混乱
     */
    private function _initData()
    {
        $this->_formID = "";
        $this->_limit = 0;
        $this->_nowAction = "";
        $this->_fields = "";
        $this->_model = [];
        $this->_data = [];
    }

    /**
     * 构造API请求格式
     * @param $parameters
     * @return string
     */
    private function _createPostData($parameters): string
    {
        return json_encode([
            'format' => 1,
            'useragent' => 'ApiClient',
            'rid' => $this->_createGUID(),
            'parameters' => $parameters,
            'timestamp' => date('Y-m-d'),
            'v' => '1.0'
        ]);
    }

    /**
     * 生成GUID
     * @return Closure
     */
    private function _createGUID()
    {
        return function () {
            $charid = strtoupper(md5(uniqid(mt_rand(), true)));
            $hyphen = chr(45); // "-"
            $uuid = chr(123) // "{"
                . substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12)
                . chr(125); // "}"
            return $uuid;
        };
    }

    /**
     * 调取接口
     * @param string $url
     * @param string $postContent
     * @param bool $isLogin
     * @return bool|string
     */
    private function _curl(string $url, string $postContent, bool $isLogin = FALSE)
    {
        $ch = curl_init($url);

        $thisHeader = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($postContent)
        );

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $thisHeader);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postContent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($isLogin) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieJar);
        } else {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieJar);
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * 获取登陆数据
     * @return array
     */
    public function getLoginData(): array
    {
        return $this->loginData;
    }
}