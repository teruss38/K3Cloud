<?php
error_reporting(E_ERROR);
ini_set('display_errors', 1);

require_once "JdService.php";
require_once "TestJd.php";


$client = new JdService("http://127.0.0.1/k3cloud/", "帐套ID", "登录名","密码");
$res = $client->login();

//testArReceivable($client);
testBill($client, 'STK_Inventory');

//单据查询
function testBill(\JdService $client, string $formID)
{
    $fields = "FID,FQty,FBaseQty";
    $res = $client->form($formID)->field($fields)->limit(10)->getBill();
    var_dump($res);
}

//应收单
function testArReceivable(\JdService $client)
{
    $formID = 'AR_receivable';
    $saveList = TestJd::testArReceivableSave($client, true);
    testApi($client, $formID, $saveList);
}

//分布式调出单
function testStkTransferOut(\JdService $client)
{
    $formID = 'STK_TRANSFEROUT';
    $saveList = TestJd::testStkTransferOutSave($client, true);
    //批量新增
    testApi($client, $formID, $saveList);
}

//TODO 分布式调入单
function testStkTransferIn(\JdService $client)
{
    $formID = 'STK_TRANSFERIN';
    $saveList = TestJd::testStkTransferInSave($client, false);
    var_dump($saveList);
//    testApi($client, $formID, $saveList);
}

//其他出库单
function testStkMisDelivery(\JdService $client)
{
    $formID = 'STK_MisDelivery';
    $saveList = TestJd::testStkMisDeliverySave($client, true);
    testApi($client, $formID, $saveList);
}

//销售退款单
function testReturnStock(\JdService $client)
{
    $formID = 'SAL_RETURNSTOCK';
    $saveList = TestJd::testSalReturnStockSave($client, false);
    testApi($client, $formID, $saveList);
}

//销售出库单
function testSalOutStock(\JdService $client)
{
    $formID = 'SAL_OUTSTOCK';
    $saveList = TestJd::testSalOutStockSave($client, true);
    testApi($client, $formID, $saveList);
}

//输出添加，查看，删除接口
function testApi(\JdService $client, string $formID, $saveList)
{
    var_dump($saveList);
//    var_dump(TestJd::testView($client, $formID, $saveList));
    var_dump(TestJd::testDelete($client, $formID, $saveList));
}





