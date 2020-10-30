<?php


class TestJd
{
    /**
     * 登陆
     * @return array
     */
    public static function testLogin()
    {
        $client = new JdService("http://47.102.103.17/k3cloud/", "5c89cb426c2c68", "演示账户8", "88888888");
        $res = $client->login();
        return $res;
    }

    /**
     * 查看
     * @param JdService $client
     * @param string $formID
     * @param array $saveList
     * @return array
     */
    public static function testView(\JdService $client, string $formID, array $saveList)
    {
        $res = $client->form($formID)->data([
            "Id" => $saveList[0]['Id']
        ])->view($formID, $data);
        return $res;
    }

    /**
     * 删除
     * @param JdService $client
     * @param string $formID
     * @param array $saveList
     * @return array|bool
     */
    public static function testDelete(\JdService $client, string $formID, array $saveList)
    {
        $idList = "";
        $numberList = [];
        foreach ($saveList as $v) {
            if (isset($v["Id"])) {
                $idList = $idList . "," . $v['Id'];
            }
            if (isset($v["Number"])) {
                $numberList[] = $v['Number'];
            }
        }
        $data = [
            "Ids" => $idList,
            "Numbers" => $numberList
        ];
        $res = $client->form($formID)->data($data)->delete();
        return $res;
    }


//    public static function testQueryInfo(\JdService $client)
//    {
//        $data = [
//            'FormId' => 'STK_Inventory'
//        ];
//        $res = $client->queryInfo('STK_Inventory', $data);
//        return $res;
//    }

    /**
     * 即时库存 - 单据查询
     * @param JdService $client
     * @param string $formID
     * @param int $limit
     * @return array
     */
    public static function testBill(\JdService $client, string $formID, string $field, array $data = [])
    {
        $res = $client->form($formID)->field($field)->data($data)->limit(10)->getBill();
        return $res;
    }

    /**
     * 应收单-保存
     * @param JdService $client
     * @param false $batch
     * @return array
     */
    public static function testArReceivableSave(\JdService $client, $batch = false)
    {
        $model = [
            "FID" => 0,
            "FBillTypeID" => [
                "FNUMBER" => "YSD01_SYS",
            ],
            "FDATE" => "2020-10-29 00:00:00",
            "FISINIT" => false,
            "FENDDATE_H" => "2020-11-30 00:00:00",
            "FCUSTOMERID" => [
                "FNumber" => "20200316l",
            ],
            "FCURRENCYID" => [
                "FNumber" => "PRE001",
            ],
            "FPayConditon" => [
                "FNumber" => "SKTJ03_SYS",
            ],
            "FISPRICEEXCLUDETAX" => true,
            "FSETTLEORGID" => [
                "FNumber" => "100",
            ],
            "FPAYORGID" => [
                "FNumber" => "100",
            ],
            "FSALEORGID" => [
                "FNumber" => "100",
            ],
            "FISTAX" => true,
            "FSALEDEPTID" => [
                "FNumber" => "100"
            ],
            "FSALEERID" => [
                "FNumber" => "ID-000027",
            ],
            "FCancelStatus" => "A",
            "FBUSINESSTYPE" => "BZ",
            "FSetAccountType" => "1",
            "FISHookMatch" => false,
            "FISINVOICEARLIER" => false,
            "FWBOPENQTY" => false,
            "FsubHeadSuppiler" => [
                "FORDERID" => [
                    "FNumber" => "20200316l",
                ],
                "FTRANSFERID" => [
                    "FNumber" => "20200316l",
                ],
                "FChargeId" => [
                    "FNumber" => "20200316l",
                ],
            ],
            "FsubHeadFinc" => [
                "FACCNTTIMEJUDGETIME" => "2020-10-29 00:00:00",
                "FSettleTypeID" => [
                    "FNumber" => "JSFS02_SYS",
                ],
                "FMAINBOOKSTDCURRID" => [
                    "FNumber" => "PRE001",
                ],
                "FEXCHANGETYPE" => [
                    "FNumber" => "HLTX01_SYS",
                ],
                "FExchangeRate" => 1.0,
                "FTaxAmountFor" => 1.6,
                "FNoTaxAmountFor" => 10.0,
            ],
            "FEntityDetail" => [
                0 => [
                    "FMATERIALID" => [
                        "FNumber" => "037000469674",
                    ],
                    "FMaterialDesc" => "OralB/欧乐B舒适深洁牙线40m成人款小巧便携式牙线盒包装美国进口",
                    "FPRICEUNITID" => [
                        "FNumber" => "Pcs"
                    ],
                    "FTaxPrice" => 500.0,
                    "FPrice" => 431.034483,
                    "FEntryTaxRate" => 16.0,
                    "FNoTaxAmountFor_D" => 10.0,
                    "FTAXAMOUNTFOR_D" => 1.6,
                    "FALLAMOUNTFOR_D" => 11.6,
                    "FDeliveryControl" => false,
                    "FStockUnitId" => [
                        "FNumber" => "Pcs",
                    ],
                    "FIsFree" => false,
                    "FSalUnitId" => [
                        "FNumber" => "Pcs",
                    ],
                    "FPriceBaseDen" => 1.0,
                    "FSalBaseNum" => 1.0,
                    "FStockBaseNum" => 1.0,
                ],
            ],
            "FEntityPlan" => [
                0 => [
                    "FENDDATE" => "2020-11-30 00:00:00",
                    "FPAYRATE" => 100.0,
                    "FPAYAMOUNTFOR" => 11.6
                ]
            ]
        ];
        $client = $client->form('AR_receivable');
        if (!$batch) {
            $res = $client->model($model)->save();
        } else {
            $models = [$model, $model];
            $res = $client->model([$model, $model])->data(["BatchCount" => count($models)])->batchSave();
        }
        return $res;
    }

    /**
     * 分步式调出单-保存
     * @param JdService $client
     * @param false $batch
     * @return array
     */
    public static function testStkTransferOutSave(\JdService $client, $batch = false)
    {
        $model = ["FID" => 0,
            "FOwnerTypeIdHead" => "BD_OwnerOrg",
            "FBillTypeID" => [
                "FNUMBER" => "FBDC01_SYS",
            ],
            "FTransferBizType" => "InnerOrgTransfer",
            "FOwnerIdHead" => [
                "FNumber" => "100",
            ],
            "FOwnerTypeInIdHead" => "BD_OwnerOrg",
            "FTransferDirect" => "GENERAL",
            "FOwnerInIdHead" => [
                "FNumber" => "100",
            ],
            "FStockOrgID" => [
                "FNumber" => "100",
            ],
            "FStockInOrgID" => [
                "FNumber" => "100",
            ],
            "FDate" => "2020-10-29 00:00:00",
            "FBaseCurrID" => [
                "FNumber" => "PRE001"
            ],
            "FVESTONWAY" => "A",
            "FSTOCKERID" => [
                "FNumber" => "222111",
            ],
            "FSTOCKERGROUPID" => [
                "FNumber" => "柔柔弱弱",
            ],
            "FBizType" => "NORMAL",
            "FSTKTRSOUTENTRY" => [
                0 => [
                    "FMaterialID" => [
                        "FNumber" => "11",
                    ],
                    "FQty" => 1.0,
                    "FUnitID" => [
                        "FNumber" => "Pcs",
                    ],
                    "FSrcStockID" => [
                        "FNumber" => "2020001",
                    ],
                    "FDestStockID" => [
                        "FNumber" => "20200201",
                    ],
                    "FSrcStockStatusID" => [
                        "FNumber" => "KCZT01_SYS",
                    ],
                    "FDestStockStatusID" => [
                        "FNumber" => "KCZT05_SYS",
                    ],
                    "FBusinessDate" => "2020-10-29 00:00:00",
                    "FOwnerTypeID" => "BD_OwnerOrg",
                    "FOwnerID" => [
                        "FNumber" => "100",
                    ],
                    "FOwnerTypeInID" => "BD_OwnerOrg",
                    "FOwnerInID" => [
                        "FNumber" => "100",
                    ],
                    "FKeeperTypeInID" => "BD_KeeperOrg",
                    "FKeeperInID" => [
                        "FNumber" => "100",
                    ],
                    "FDestMaterialID" => [
                        "FNumber" => "11",
                    ],
                    "FBaseUnitID" => [
                        "FNumber" => "Pcs",
                    ],
                    "FKeeperTypeID" => "BD_KeeperOrg",
                    "FKeeperID" => [
                        "FNumber" => "100",
                    ],
                    "FCheckDelivery" => false,
                ],
            ],
        ];
        $client = $client->form('STK_TRANSFEROUT');
        if (!$batch) {
            $res = $client->model($model)->save();
        } else {
            $models = [$model, $model];
            $res = $client->model([$model, $model])->data(["BatchCount" => count($models)])->batchSave();
        }
        return $res;
    }

    /**
     * 分步式调入单-保存
     * @param JdService $client
     * @param false $batch
     * @return array
     */
    public static function testStkTransferInSave(\JdService $client, $batch = false, $dataHeader = [])
    {
        $model = [
            "FID" => 0,
            "FBillTypeID" => [
                "FNUMBER" => "FBDR01_SYS",
            ],
            "FTransferBizType" => "InnerOrgTransfer",
            "FTransferDirect" => "GENERAL",
            "FStockOrgID" => [
                "FNumber" => "100",
            ],
            "FStockOutOrgID" => [
                "FNumber" => "100",
            ],
            "FTransferMode" => "INDIRECT",
            "FDate" => "2020-10-29 00:00:00",
            "FOwnerTypeOutIdHead" => "BD_OwnerOrg",
            "FOwnerOutIdHead" => [
                "FNumber" => "110",
            ],
            "FSTOCKERID" => [
                "FNumber" => "222111"
            ],
            "FUpdateTime" => "2020-10-29",
            "FBizType" => "NORMAL",
            "FVESTONWAY" => "B",
            "FSTOCKERGROUPID" => [
                "FNumber" => "柔柔弱弱",
            ],
            "FOwnerTypeIdHead" => "BD_OwnerOrg",
            "FSTKTRSINENTRY" => [
                0 => [
                    "FSrcStockStatusID" => [
                        "FNumber" => "KCZT05_SYS",
                    ],
                    "FDestStockStatusID" => [
                        "FNumber" => "KCZT01_SYS",
                    ],
                    "FKeeperTypeOutID" => "BD_KeeperOrg",
                    "FKeeperOutID" => [
                        "FNumber" => "100",
                    ],
                    "FOwnerTypeOutID" => "BD_OwnerOrg",
                    "FOwnerTypeID" => "BD_OwnerOrg",
                    "FBusinessDate" => "2020-10-29 00:00:00",
                    "FKeeperTypeID" => "BD_KeeperOrg",
                    "FKeeperID" => [
                        "FNumber" => "100",
                    ],
                ],
            ],
        ];
        $client = $client->form('STK_TRANSFERIN');
        if (!$batch) {
            $res = $client->model($model)->save();
        } else {
            $models = [$model, $model];
            $res = $client->model([$model, $model])->data(["BatchCount" => count($models)])->batchSave();
        }
        return $res;
    }

    /**
     * 即时库存-保存
     * @param JdService $client
     * @param false $batch
     * @return array
     */
    public static function testStkInventorySave(\JdService $client, $batch = false)
    {
        $model = [
            "FID" => 0,
            "FOwnerTypeId" => "BD_OwnerOrg",
            "FKeeperTypeId" => "BD_KeeperOrg",
            "FMaterialId" => [
                "FNumber" => "569849899630",
            ],
            "FUpdateTime " => "2020-10-28",
        ];
        $client = $client->form('STK_Inventory');
        if (!$batch) {
            $res = $client->model($model)->save();
        } else {
            $models = [$model, $model];
            $res = $client->model([$model, $model])->data(["BatchCount" => count($models)])->batchSave();
        }
        return $res;
    }

    /**
     * 其它出库单-保存
     * @param JdService $client
     * @param bool $batch
     * @return array
     */
    public static function testStkMisDeliverySave(\JdService $client, bool $batch = false)
    {
        $model = [
            "FID" => 0,
            "FBillTypeID" => [
                "FNUMBER" => "QTCKD01_SYS",
            ],
            "FStockOrgId" => [
                "FNumber" => "100"
            ],
            "FPickOrgId" => [
                "FNumber" => "100",
            ],
            "FStockDirect" => "GENERAL",
            "FDate" => "2020-10-28 00:00:00",
            "FCustId" => [
                "FNumber" => "20200201",
            ],
            "FDeptId" => [
                "FNumber" => "BM000001",
            ],
            "FStockerId" => [
                "FNAME" => "曹操",
            ],
            "FStockerGroupId" => [
                "FNumber" => "柔柔弱弱",
            ],
            "FOwnerTypeIdHead" => "BD_OwnerOrg",
            "FOwnerIdHead" => [
                "FNumber" => "100",
            ],
            "FNote" => "6666",
            "FBaseCurrId" => [
                "FNumber" => "PRE001"
            ],
            "FEntity" => [
                0 => [
                    "FMaterialId" => [
                        "FNumber" => "20200401C",
                    ],
                    "FUnitID" => [
                        "FNumber" => "double",
                    ],
                    "FQty" => 1.0,
                    "FBaseUnitId" => [
                        "FNumber" => "Pcs",
                    ],
                    "FStockId" => [
                        "FNumber" => "2020002"
                    ],
                    "FOwnerTypeId" => "BD_OwnerOrg",
                    "FOwnerId" => [
                        "FNumber" => "100",
                    ],
                    "FEntryNote" => "7777",
                    "FStockStatusId" => [
                        "FNumber" => "KCZT01_SYS",
                    ],
                    "FKeeperTypeId" => "BD_KeeperOrg",
                    "FDistribution" => false,
                    "FKeeperId" => [
                        "FNumber" => "100",
                    ],
                ]
            ]
        ];
        $client = $client->form('STK_MisDelivery');
        if (!$batch) {
            $res = $client->model($model)->save();
        } else {
            $models = [$model, $model];
            $res = $client->model([$model, $model])->data(["BatchCount" => count($models)])->batchSave();
        }
        return $res;
    }

    /**
     * 销售退款单-保存
     * @param JdService $client
     * @param bool $batch
     * @return array
     */
    public static function testSalReturnStockSave(\JdService $client, bool $batch = false)
    {
        $model = [
            "FID" => 0,
            "FBillTypeID" => [
                "FNUMBER" => "XSTHD01_SYS",
            ],
            "FDate" => "2020-10-28 00:00:00",
            "FSaleOrgId" => [
                "FNumber" => "100",
            ],
            "FRetcustId" => [
                "FNumber" => "20190910",
            ],
            "FReturnReason" => [
                "FNumber" => "PS",
            ],
            "FHeadLocId" => [
                "FNumber" => "BIZ20200908142855",
            ],
            "FTransferBizType" => [
                "FNumber" => "OverOrgSal",
            ],
            "FStockOrgId" => [
                "FNumber" => "100",
            ],
            "FStockDeptId" => [
                "FNumber" => "BM000003",
            ],
            "FStockerGroupId" => [
                "FNumber" => "柔柔弱弱",
            ],
            "FStockerId" => [
                "FNumber" => "222111",
            ],
            "FReceiveCustId" => [
                "FNumber" => "20190910",
            ],
            "FReceiveAddress" => "上海浦东新区xxxx号",
            "FSettleCustId" => [
                "FNumber" => "20190910",
            ],
            "FReceiveCusContact" => [
                "FNAME" => "333333",
            ],
            "FPayCustId" => [
                "FNumber" => "20190910"
            ],
            "FOwnerTypeIdHead" => "BD_OwnerOrg",
            "FIsTotalServiceOrCost" => false,
            "SubHeadEntity" => [
                "FSettleCurrId" => [
                    "FNumber" => "PRE001",
                ],
                "FSettleOrgId" => [
                    "FNumber" => "100",
                ],
                "FLocalCurrId" => [
                    "FNumber" => "PRE001",
                ],
                "FExchangeTypeId" => [
                    "FNumber" => "HLTX01_SYS",
                ],
                "FExchangeRate" => 1.0,
            ],
            "FEntity" => [
                0 => [
                    "FRowType" => "Standard",
                    "FMaterialId" => [
                        "FNumber" => "1111111",
                    ],
                    "FUnitID" => [
                        "FNumber" => "Pcs",
                    ],
                    "FStockStatusID" => [
                        "FNumber" => "KCZT01_SYS",
                    ],
                    "FStockID" => [
                        "FNumber" => "0301",
                    ],
                    "FRealQty" => 1.0,
                    "FIsFree" => false,
                    "FEntryTaxRate" => 13.0,
                    "FReturnType" => [
                        "FNumber" => "THLX01_SYS",
                    ],
                    "FOwnerTypeId" => "BD_OwnerOrg",
                    "FOwnerId" => [
                        "FNumber" => "100",
                    ],
                    "FDeliveryDate" => "2020-10-28 00:00:00",
                    "FSalUnitID" => [
                        "FNumber" => "Pcs",
                    ],
                    "FSalUnitQty" => 1.0,
                    "FSalBaseQty" => 1.0,
                    "FPriceBaseQty" => 1.0,
                    "FIsOverLegalOrg" => false,
                    "FARNOTJOINQTY" => 1.0,
                    "FIsReturnCheck" => false,
                ],
            ],
        ];
        $client = $client->form('SAL_RETURNSTOCK');
        if (!$batch) {
            $res = $client->model($model)->save();
        } else {
            $models = [$model, $model];
            $res = $client->model($models)->data(["BatchCount" => count($models)])->batchSave();
        }
        return $res;
    }

    /**
     * 销售出库单-保存
     * @param JdService $client
     * @param bool $batch
     * @return array
     */
    public static function testSalOutStockSave(\JdService $client, bool $batch = false)
    {
        $model = [
            "FID" => 0,
            "FBillTypeID" => [
                "FNUMBER" => "XSCKD01_SYS",
            ],
            "FDate" => "2020-10-27 00:00:00",
            "FSaleOrgId" => [
                "FNumber" => "100",
            ],
            "FCustomerID" => [
                "FNumber" => "20200319",
            ],
            "FStockOrgId" => [
                "FNumber" => "100",
            ],
            "FDeliveryDeptID" => [
                "FNumber" => "BM000003",
            ],
            "FStockerGroupID" => [
                "FNumber" => "柔柔弱弱",
            ],
            "FStockerID" => [
                "FNumber" => "222111",
            ],
            "FReceiverID" => [
                "FNumber" => "20200319",
            ],
            "FSettleID" => [
                "FNumber" => "20200319",
            ],
            "FPayerID" => [
                "FNumber" => "20200319",
            ],
            "FOwnerTypeIdHead" => "BD_OwnerOrg",
            "FIsTotalServiceOrCost" => false,
            "SubHeadEntity" => [
                "FSettleCurrID" => [
                    "FNumber" => "PRE001",
                ],
                "FSettleOrgID" => [
                    "FNumber" => "100",
                ],
                "FIsIncludedTax" => true,
                "FLocalCurrID" => [
                    "FNumber" => "PRE001",
                ],
                "FExchangeTypeID" => [
                    "FNumber" => "HLTX01_SYS",
                ],
                "FExchangeRate" => 1.0,
                "FIsPriceExcludeTax" => true,
            ],
            "FEntity" => [
                0 => [
                    "FRowType" => "Standard",
                    "FMaterialID" => [
                        "FNumber" => "1.13new",
                    ],
                    "FUnitID" => [
                        "FNumber" => "Pcs",
                    ],
                    "FRealQty" => 1.0,
                    "FIsFree" => false,
                    "FOwnerTypeID" => "BD_OwnerOrg",
                    "FOwnerID" => [
                        "FNumber" => "100",
                    ],
                    "FEntryTaxRate" => 13.0,
                    "FStockStatusID" => [
                        "FNumber" => "KCZT01_SYS",
                    ],
                    "FSalUnitID" => [
                        "FNumber" => "Pcs",
                    ],
                    "FStockID" => [
                        "FNumber" => "0301",
                    ],
                    "FSALUNITQTY" => 1.0,
                    "FSALBASEQTY" => 1.0,
                    "FPRICEBASEQTY" => 1.0,
                    "FOUTCONTROL" => false,
                    "FIsOverLegalOrg" => false,
                    "FARNOTJOINQTY" => 1.0,
                    "FCheckDelivery" => false,
                ],
            ],
        ];
        $client = $client->form('SAL_OUTSTOCK');
        if (!$batch) {
            $res = $client->model($model)->save();
        } else {
            $models = [$model, $model];
            $res = $client->model([$model, $model])->data(["BatchCount" => count($models)])->batchSave();
        }
        return $res;
    }
}