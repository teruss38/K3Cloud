<?php

trait JdBuild
{
    protected $_data = [];
    protected $_model = [];
    protected $_formID = "";
    protected $_limit = 0;
    protected $_fields = "";
    protected $_nowAction = "";

//    public function __call($name, $args)
//    {
//        $this->value = call_user_func($name, $this->value, $args[0]);
//        return $this;
//    }

    /**
     * formID属性构造器
     * @param string $formID
     * @return $this
     */
    public function form(string $formID = '')
    {
        $this->_formID = $formID;
        return $this;
    }

    /**
     * Model属性构造器
     * @param array $model
     * @return $this
     */
    public function model(array $model = [])
    {
        $this->_model = $model;
        return $this;
    }

    /**
     * data各属性构造器
     * @param array $data
     * @return $this
     */
    public function data(array $data = [])
    {
        $this->_data = [];
        foreach ($data as $k => $v) {
            $this->_data[ucfirst($k)] = $v;
        }
        return $this;
    }

    /**
     * getBill-limit构造器
     * @param int $limitNum 获取的个数
     * @return $this
     */
    public function limit(int $limitNum)
    {
        $this->_limit = $limitNum;
        return $this;
    }

    /**
     * getBill-fieldKey构造器
     * @param string $field 获取的字段
     * @return $this、
     */
    public function field(string $fields)
    {
        $this->_fields = $fields;
        return $this;
    }


}