<?php

class GeneralSettings extends CiiSettingsModel
{
	protected $name = NULL;

	protected $dateFormat = 'F jS, Y';

	protected $timeFormat = 'H:i';

	protected $defaultLanguage = 'en_US';

	protected $forceSecureSSL = false;

	protected $offline = 0;

	protected $bcrypt_cost = 13;

	protected $searchPaginationSize = 10;

	protected $categoryPaginationSize = 10;

	protected $contentPaginationSize = 10;

	protected $useDisqusComments = 0;

	protected $disqus_shortname = NULL;

	//protected $useDiscourseComments = 0;

	//protected $discourseUrl = '';

	protected $useOpenstackCDN = false;
	
	protected $useRackspaceCDN = false;

	protected $openstack_identity = NULL;

	protected $openstack_username = NULL;

	protected $openstack_apikey = NULL;

	protected $openstack_region = NULL;

	protected $openstack_container = NULL;

	public function groups()
	{
		return array(
			Yii::t('ciims.models.general', 'Site Settings') => array('name', 'offline', 'forceSecureSSL', 'bcrypt_cost', 'categoryPaginationSize','contentPaginationSize','searchPaginationSize'),
			Yii::t('ciims.models.general', 'Disqus') => array('useDisqusComments', 'disqus_shortname'),
			//Yii::t('ciims.models.general', 'Discourse') => array('useDiscourseComments', 'discourseUrl'),
			Yii::t('ciims.models.general', 'Display Settings') => array('dateFormat', 'timeFormat', 'defaultLanguage'),
			Yii::t('ciims.models.general', 'Upload Settings') => array('useOpenstackCDN', 'useRackspaceCDN', 'openstack_identity', 'openstack_username', 'openstack_apikey', 'openstack_region', 'openstack_container')
		);
	}

	/**
	 * Validation rules
	 * @return array
	 */
	public function rules()
	{
		return array(
			array('name, dateFormat, timeFormat, defaultLanguage', 'required'),
			array('name', 'length', 'max' => 255),
			array('dateFormat, timeFormat, defaultLanguage', 'length', 'max' => 25),
			array('offline, useDisqusComments, forceSecureSSL, useOpenstackCDN, useRackspaceCDN', 'boolean'),
			array('disqus_shortname, openstack_identity, openstack_username, openstack_apikey, openstack_region, openstack_container', 'length', 'max' => 255),
			array('bcrypt_cost', 'numerical', 'integerOnly'=>true, 'min' => 13),
			array('searchPaginationSize, categoryPaginationSize, contentPaginationSize', 'numerical', 'integerOnly' => true, 'min' => 10),
			//array('url', 'url')
		);
	}

	/**
	 * Attribute labels
	 * @return array
	 */
	public function attributeLabels()
	{
		return array(
			'name' => Yii::t('ciims.models.general', 'Site Name'),
			'dateFormat' => Yii::t('ciims.models.general', 'Date Format'),
			'timeFormat' => Yii::t('ciims.models.general', 'Time Format'),
			'defaultLanguage' => Yii::t('ciims.models.general', 'Default Language'),
			'offline' => Yii::t('ciims.models.general', 'Offline Mode'),
			'forceSecureSSL' => Yii::t('ciims.models.general', 'Force SSL for Secure Areas'),
			'bcrypt_cost' => Yii::t('ciims.models.general', 'Password Strength Settings'),
			'searchPaginationSize' => Yii::t('ciims.models.general', 'Search Post Count'),
			'categoryPaginationSize' => Yii::t('ciims.models.general', 'Category Post Count'),
			'contentPaginationSize' => Yii::t('ciims.models.general', 'Content Post Cost'),

			// Discourse
			'useDisqusComments'    => Yii::t('ciims.models.general', 'Use Disqus Comments'),
			'disqus_shortname'     => Yii::t('ciims.models.general', 'Disqus Shortcode'),

			// Discourse
			//'useDiscourseComments' => Yii::t('ciims.models.general', 'Use Discourse Comments'),
			//'discourseUrl' => Yii::t('ciims.models.general', 'Discourse URL'),

			// Openstack Data
			'useOpenstackCDN' => Yii::t('ciims.models.general', 'Use Openstack for Uploads?'),
			'useRackspace CDN' => Yii::t('ciims.models.general', 'Use Rackspace CDN?'),
			'openstack_identity' => Yii::t('ciims.models.general', 'Openstack Identity URL'),
			'openstack_username' => Yii::t('ciims.models.general', 'Openstack Username'),
			'openstack_apikey' => Yii::t('ciims.models.general', 'Openstack API Key'),
			'openstack_region' => Yii::t('ciims.models.general', 'Openstack Region'),
			'openstack_container' => Yii::t('ciims.models.general', 'Openstack Container Name'),
		);
	}

	/**
	 * Overload the __getter so that it checks for data in the following order
	 * 1) Pull From db/cache (Cii::getConfig now does caching of elements for improved performance)
	 * 2) Check for __protected__ property, which we consider the default vlaue
	 * 3) parent::__get()
	 *
	 * In order for this to work with __default__ values, the properties in classes that extend from this
	 * MUST be protected. If they are public it will bypass this behavior.
	 * 
	 * @param  mixed $name The variable name we want to retrieve from the calling class
	 * @return mixed
	 */
	public function __get($name)
	{
		$data = Cii::getConfig($name);

		if ($data !== NULL && $data !== "" && !isset($this->attributes[$name]))
		{
			if ($name == 'openstack_apikey')
				return Cii::decrypt($data);
			return $data;
		}

		if (property_exists($this, $name))
		{
			if ($name == 'openstack_apikey')
				return Cii::decrypt($this->$name);
			return $this->$name;
		}

		return parent::__get($name);
	}

	/**
	 * Allow some override values
	 * @return parent::beforeSave();
	 */
	public function beforeSave()
	{
		// Encrypt the Openstack API Key
		if ($this->attributes['openstack_apikey'] != NULL && $this->attributes['openstack_apikey'] != "")
			$this->attributes['openstack_apikey'] = $this->openstack_apikey = Cii::encrypt($this->attributes['openstack_apikey']);

		return parent::beforeSave();
	}
}
