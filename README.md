# 金蝶云Web api文档
>作者 Hogen

### 调用方式
1. 引入bin中的SDK
2. Web Api
* Kingdee.BOS.WebApi.FormService.dll
此组装件包含WebAPI主要接口的功能实现。部署在应用层服务器。
* Kingdee.BOS.WebApi.ServicesStub.dll
此组装件主要包含WebAPI接口定义，扩展接口定义以及登陆验证接口。部署在应用层服务器。
* Kingdee.BOS.WebApi.Client.dll
此组装件为WebAPI的客户端组件，封装了一些在异构系统客户端访问WebAPI的方法，适用于C#程序调用。由于它应用于异构系统客户端，所以此组装件需要拷贝到异构系统客户端环境中。非C#程序调用可以不用拷贝。

### 开发文档
业务对象表单
> https://open.kingdee.com/K3Cloud/Open/ApiCenterReportDetail.aspx#aimin2

|  FORMID  | 模块 |
| ------ | ------ |
| SAL_OUTSTOCK | 供应链-销售-销售出库单 | 
| SAL_RETURNSTOCK | 供应链-销售-销售退货单 |
| STK_MisDelivery | 供应链-库存-其他出库单 |
| STK_Inventory  | 供应链-库存-即时库存 |
| STK_TRANSFERIN | 供应链-库存-分布式调入单 |
| STK_TRANSFEROUT | 供应链-库存-分布式调出单 |
| AR_receivable | 财务会计-应收款-应收单 |


测试案例参考TestJd.php和test.php

- 登陆
```Kingdee.BOS.WebApi.ServicesStub.AuthService.ValidateUser.common.kdsvc```
```php
//默认每次调用接口前先login，因为不知道login的时效性，没有做单例模式。
$client = new JdService("http://127.0.0.1/k3cloud/", "帐套ID", "登录名","密码");
$res = $client->Login();
if ($res['LoginResultType'] != 1) {
    // 登录失败
}
```

---
- 查看
```Kingdee.BOS.WebApi.ServicesStub.DynamicFormService.View.common.kdsvc```

|  变量  | 参数 | 必须? |
| ------ | ------ | ------ |
| Number/Id | 单号/Id | * |
| CreateOrgId| 创建者组织内码| |

```php
$data= [
    "Number"=>"",   
    "Id"=>""
];
$res = $client->form($formID)->data($data)->view();
```
---
- 保存
```Kingdee.BOS.WebApi.ServicesStub.DynamicFormService.Save.common.kdsvc```
>https://open.kingdee.com/K3Cloud/Open/ApiCenterReportDetail.aspx#aimin2

文档过旧，有些Model必要的参数没标识
```php
$data = [
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
     //"Model"=>$model    使用model($model)传递
];
$model = [
    ···
];
$res = $client->form('STK_TRANSFERIN')->data($data)->model($model)->save();
```
* Request

| Model内变量  | 参数  | FormID |ex |
| ------ | ------ | ------ | ------ |
| FUpdateTime| 最后更新日期 |STK_Inventory STK_TRANSFERIN |"2020-10-28"|

| FEntity内变量  | 参数 | FormID |ex |
| ------ | ------ |  ------ | ------ |
| FStockID | 仓库名 | | |
| FStockstatusId| 库存状态 | ||
| FUnitID | 库存单位 |||
| FBaseUnitQty | 库存基本数量 | ||
| FRealQty  | 实发数量 | ||
| FSrcStockID | 调出仓库 | STK_TRANSFEROUT |"FSrcStockID" =>["FNumber" => "2020001"]|

* Response

```php
$response = [
    0=>[
         'Id' => 100468,    //ID
         'Number' => 'XSTHD000385', //单号
         'DIndex' => 0
    ],1=>[
    ]
];
```

---
- 批量保存
```Kingdee.BOS.WebApi.ServicesStub.DynamicFormService.BatchSave.common.kdsvc```

```php
$data = [
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
     //"Model"=>$model    使用model($model)传递
    /**
    * 非必须。此参数主要用于优化性能，当传入的单据数据量较大时，可以设定此参数的并行分批执行次数。
    * 例如传入100张单据数据，此参数设定为10，则表示在k3cloud系统中，以10个单据为一批，分10批，同时并发保存，提升效率。
    * 数据包参数格式和Save接口的类似，主要差别在于批量保存是传入多张单据的数据，Modle数据用[]括起来，而Save仅传入一张单据数据。
    **/
    "BatchCount"=>0
];

//批量保存
$models = [$model1,$model2];
$res = $client->form('STK_TRANSFERIN')->data($data)->model($models)->batchSave();
```
Response

|  变量  | 参数 |
| ------ | ------ | 
| Id | Id | 
| Number | 单号 | 
---
- 删除
```Kingdee.BOS.WebApi.ServicesStub.DynamicFormService.Delete.common.kdsvc```

```php
   $data = [
       "CreateOrgId" => 0,
       "Numbers" => [],
       "Ids" => "",
   ];
    $res = $client->form($formID)->data($data)->delete();
```

---
- 单据查询
```Kingdee.BOS.WebApi.ServicesStub.DynamicFormService.ExecuteBillQuery.common.kdsvc```
```php
$data = [
//    'FormId' => "STK_Inventory",  使用form传递
//    "FieldKeys" => "",      使用field传递  获取字段参数 ex:FID,FBaseQty,FMaterialId
    "FilterString" => "", // 过滤条件 ex:"FMaterialId.FNumber='HG_TEST'"
    "OrderString" => "", // 排序条件 ex:FID ASC
    "TopRowCount" => 0, // 最多允许查询的数量，0或者不要此属性表示不限制
    "StartRow" => 0,   // 分页取数开始行索引，从0开始，例如每页10行数据，第2页开始是10，第3页开始是20
//    "Limit" => 0,    使用limit传递 分页取数每页允许获取的数据，最大不能超过200
];
$res = $client->form('STK_Inventory')->field("FID,FBaseQty")->data($data)->limit(10)->getBill();
```

---
- TODO 暂存
```Kingdee.BOS.WebApi.ServicesStub.DynamicFormService.Draft.common.kdsvc```
---
- TODO 元数据查询
```Kingdee.BOS.WebApi.ServicesStub.DynamicFormService.QueryBusinessInfo.common.kdsvc```
---
- TODO 审核
```K3Cloud/Kingdee.BOS.WebApi.ServicesStub.DynamicFormService.Audit.common.kdsvc```
---
- TODO 反审核
```K3Cloud/Kingdee.BOS.WebApi.ServicesStub.DynamicFormService.UnAudit.common.kdsvc```
---
- TODO 分配表单
```K3Cloud/Kingdee.BOS.WebApi.ServicesStub.DynamicFormService.Allocate.common.kdsvc```
---
- TODO 提交表单
```K3Cloud/Kingdee.BOS.WebApi.ServicesStub.DynamicFormService.Submit.common.kdsvc```
---
- TODO 下推
```K3Cloud/Kingdee.BOS.WebApi.ServicesStub.DynamicFormService.Push.common.kdsvc```
---
- TODO 分组保存
```K3Cloud/Kingdee.BOS.WebApi.ServicesStub.DynamicFormService.GroupSave.common.kdsvc```


