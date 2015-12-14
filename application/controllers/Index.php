<?php

class IndexController extends BaseController
{
	public function indexAction()
	{
		$page = $this->getRequest()->get('page', 1);
		$limit = 10;		
		$offset = ($page-1)*$limit;
		
		// 当前页的条数
		$lists = $this->getDb()->table('loto_userbets')->limit($offset, $limit)->select();
		
		// 数据库一共有几条
		$count = $this->getDb()->field('count("id")')->table('loto_userbets')->select('fetchColumn');
		
		$page = new \Html\Page($count, $limit, 10);
		$page->create();
		$page->output();
		
		exit;		
	}
	
	public function getDb() {
		$driver['host'] = '127.0.0.1';
		$driver['port'] = 3306;
		$driver['dbname'] = 'my5755';
		$driver['username'] = 'root';
		$driver['password'] = '123456';
		$driver['charset'] = 'utf8';
		return \Driver\Mysql::getInstance($driver);
	}
}