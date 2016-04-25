<?php

/**
 * 首页表单
 * @author eny
 *
 */
class IndexForm {
	public static function indexInput() {
		return array(
			// id => array('请求方式', '检查方式', '是否必须', '错误提示', '可选配置选项', '默认值', '别名');
			'id' => array('GET', 'number', 'require', '请输入正确的id值', array('min'=>1)),
		);
		
/* 		if($rule[0] && strcasecmp($this->requestMethod, $check[0])) {
			continue;
		}
		$this->rules[$key]['value'] = isset($params[$key]) ? $params[$key] : NULL;
		$this->rules[$key]['method'] = $check[1];
		$this->rules[$key]['require'] = $check[2];
		$this->rules[$key]['notify'] = $check[3];
		$this->rules[$key]['options'] = isset($check[4]) ? $check[4] : NULL;
		$this->rules[$key]['default'] = isset($check[5]) ? $check[5] : NULL;
		$this->rules[$key]['alias'] = isset($check[6]) ? $check[6] : NULL; */
	}
}