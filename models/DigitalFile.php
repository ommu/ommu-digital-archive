<?php
/**
 * DigitalFile
 *
 * @author Putra Sudaryanto <putra@ommu.co>
 * @contact (+62)856-299-4114
 * @copyright Copyright (c) 2016 Ommu Platform (www.ommu.co)
 * @created date 24 October 2016, 06:49 WIB
 * @link https://github.com/ommu/mod-digital-archive
 *
 * This is the template for generating the model class of a specified table.
 * - $this: the ModelCode object
 * - $tableName: the table name for this class (prefix is already removed if necessary)
 * - $modelClass: the model class name
 * - $columns: list of table columns (name=>CDbColumnSchema)
 * - $labels: list of attribute labels (name=>label)
 * - $rules: list of validation rules
 * - $relations: list of relations (name=>relation declaration)
 *
 * --------------------------------------------------------------------------------------
 *
 * This is the model class for table "ommu_digital_file".
 *
 * The followings are the available columns in table 'ommu_digital_file':
 * @property string $file_id
 * @property integer $publish
 * @property string $digital_id
 * @property string $digital_filename
 * @property string $creation_date
 * @property string $creation_id
 * @property string $modified_date
 * @property string $modified_id
 */
class DigitalFile extends CActiveRecord
{
	use UtilityTrait;
	use GridViewTrait;

	public $defaultColumns = array();
	public $digital_title_input;
	public $old_digital_filename_input;
	public $md5filepath;
	
	// Variable Search
	public $digital_search;
	public $creation_search;
	public $modified_search;

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DigitalFile the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ommu_digital_file';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('digital_id', 'required'),
			array('
				digital_title_input', 'required', 'on'=>'formFileInsert'),
			array('publish', 'numerical', 'integerOnly'=>true),
			array('digital_id, creation_id, modified_id', 'length', 'max'=>11),
			array('digital_filename, 				
				digital_title_input, old_digital_filename_input, md5filepath', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('file_id, publish, digital_id, digital_filename, creation_date, creation_id, modified_date, modified_id,
				md5filepath, digital_search, creation_search, modified_search', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'view' => array(self::BELONGS_TO, 'ViewDigitalFile', 'file_id'),
			'digital' => array(self::BELONGS_TO, 'Digitals', 'digital_id'),
			'creation' => array(self::BELONGS_TO, 'Users', 'creation_id'),
			'modified' => array(self::BELONGS_TO, 'Users', 'modified_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'file_id' => Yii::t('attribute', 'File'),
			'publish' => Yii::t('attribute', 'Publish'),
			'digital_id' => Yii::t('attribute', 'Digital'),
			'digital_filename' => Yii::t('attribute', 'File'),
			'creation_date' => Yii::t('attribute', 'Creation Date'),
			'creation_id' => Yii::t('attribute', 'Creation'),
			'modified_date' => Yii::t('attribute', 'Modified Date'),
			'modified_id' => Yii::t('attribute', 'Modified'),
			'digital_title_input' => Yii::t('attribute', 'Digital Title'),
			'old_digital_filename_input' => Yii::t('attribute', 'Old File'),
			'md5filepath' => Yii::t('attribute', 'File Path'),
			'digital_search' => Yii::t('attribute', 'Digital'),
			'creation_search' => Yii::t('attribute', 'Creation'),
			'modified_search' => Yii::t('attribute', 'Modified'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;
		
		// Custom Search
		$criteria->with = array(
			'digital' => array(
				'alias' => 'digital',
				'select' => 'publish, digital_title',
			),
			'creation' => array(
				'alias' => 'creation',
				'select' => 'displayname',
			),
			'modified' => array(
				'alias' => 'modified',
				'select' => 'displayname',
			),
		);

		$criteria->compare('t.file_id', strtolower($this->file_id), true);
		if(Yii::app()->getRequest()->getParam('type') == 'publish')
			$criteria->compare('t.publish', 1);
		elseif(Yii::app()->getRequest()->getParam('type') == 'unpublish')
			$criteria->compare('t.publish', 0);
		elseif(Yii::app()->getRequest()->getParam('type') == 'trash')
			$criteria->compare('t.publish', 2);
		else {
			$criteria->addInCondition('t.publish', array(0,1));
			$criteria->compare('t.publish', $this->publish);
		}
		if(Yii::app()->getRequest()->getParam('digital'))
			$criteria->compare('t.digital_id', Yii::app()->getRequest()->getParam('digital'));
		else
			$criteria->compare('t.digital_id', $this->digital_id);
		$criteria->compare('t.digital_filename', strtolower($this->digital_filename), true);
		if($this->creation_date != null && !in_array($this->creation_date, array('0000-00-00 00:00:00','1970-01-01 00:00:00','0002-12-02 07:07:12','-0001-11-30 00:00:00')))
			$criteria->compare('date(t.creation_date)', date('Y-m-d', strtotime($this->creation_date)));
		if(Yii::app()->getRequest()->getParam('creation'))
			$criteria->compare('t.creation_id', Yii::app()->getRequest()->getParam('creation'));
		else
			$criteria->compare('t.creation_id', $this->creation_id);
		if($this->modified_date != null && !in_array($this->modified_date, array('0000-00-00 00:00:00','1970-01-01 00:00:00','0002-12-02 07:07:12','-0001-11-30 00:00:00')))
			$criteria->compare('date(t.modified_date)', date('Y-m-d', strtotime($this->modified_date)));
		if(Yii::app()->getRequest()->getParam('modified'))
			$criteria->compare('t.modified_id', Yii::app()->getRequest()->getParam('modified'));
		else
			$criteria->compare('t.modified_id', $this->modified_id);
		
		$criteria->compare('digital.digital_title', strtolower($this->digital_search), true);
		if(Yii::app()->getRequest()->getParam('digital') && Yii::app()->getRequest()->getParam('publish'))
			$criteria->compare('digital.publish', Yii::app()->getRequest()->getParam('publish'));
		$criteria->compare('creation.displayname', strtolower($this->creation_search), true);
		$criteria->compare('modified.displayname', strtolower($this->modified_search), true);

		if(!Yii::app()->getRequest()->getParam('DigitalFile_sort'))
			$criteria->order = 't.file_id DESC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>30,
			),
		));
	}


	/**
	 * Get column for CGrid View
	 */
	public function getGridColumn($columns=null) {
		if($columns !== null) {
			foreach($columns as $val) {
				/*
				if(trim($val) == 'enabled') {
					$this->defaultColumns[] = array(
						'name'  => 'enabled',
						'value' => '$data->enabled == 1? "Ya": "Tidak"',
					);
				}
				*/
				$this->defaultColumns[] = $val;
			}
		} else {
			//$this->defaultColumns[] = 'file_id';
			$this->defaultColumns[] = 'publish';
			$this->defaultColumns[] = 'digital_id';
			$this->defaultColumns[] = 'digital_filename';
			$this->defaultColumns[] = 'creation_date';
			$this->defaultColumns[] = 'creation_id';
			$this->defaultColumns[] = 'modified_date';
			$this->defaultColumns[] = 'modified_id';
		}

		return $this->defaultColumns;
	}

	/**
	 * Set default columns to display
	 */
	protected function afterConstruct() {
		if(count($this->defaultColumns) == 0) {
			/*
			$this->defaultColumns[] = array(
				'class' => 'CCheckBoxColumn',
				'name' => 'id',
				'selectableRows' => 2,
				'checkBoxHtmlOptions' => array('name' => 'trash_id[]')
			);
			*/
			$this->defaultColumns[] = array(
				'header' => 'No',
				'value' => '$this->grid->dataProvider->pagination->currentPage*$this->grid->dataProvider->pagination->pageSize + $row+1'
			);
			if(!Yii::app()->getRequest()->getParam('digital')) {
				$this->defaultColumns[] = array(
					'name' => 'digital_search',
					'value' => '$data->digital->digital_title',
				);
			}
			$this->defaultColumns[] = 'digital_filename';
			$this->defaultColumns[] = array(
				'name' => 'creation_search',
				'value' => '$data->creation->displayname',
			);
			$this->defaultColumns[] = array(
				'name' => 'creation_date',
				'value' => 'Yii::app()->dateFormatter->formatDateTime($data->creation_date, \'medium\', false)',
				'htmlOptions' => array(
					'class' => 'center',
				),
				'filter' => $this->filterDatepicker($this, 'creation_date'),
			);
			if(!Yii::app()->getRequest()->getParam('type')) {
				$this->defaultColumns[] = array(
					'name' => 'publish',
					'value' => 'Utility::getPublish(Yii::app()->controller->createUrl(\'publish\', array(\'id\'=>$data->file_id)), $data->publish, 1)',
					'htmlOptions' => array(
						'class' => 'center',
					),
					'filter' => $this->filterYesNo(),
					'type' => 'raw',
				);
			}
		}
		parent::afterConstruct();
	}

	/**
	 * User get information
	 */
	public static function getInfo($id, $column=null)
	{
		if($column != null) {
			$model = self::model()->findByPk($id, array(
				'select' => $column,
			));
 			if(count(explode(',', $column)) == 1)
 				return $model->$column;
 			else
 				return $model;
			
		} else {
			$model = self::model()->findByPk($id);
			return $model;
		}
	}
	
	protected function afterFind() {
		$this->md5filepath = md5($this->digital->view->md5path.$this->creation_date);
		
		parent::afterFind();		
	}

	/**
	 * before validate attributes
	 */
	protected function beforeValidate() {
		$controller = strtolower(Yii::app()->controller->id);
		$setting = DigitalSetting::model()->findByPk(1, array(
			'select' => 'digital_global_file_type, digital_file_type, form_standard, form_custom_field',
		));
		$digital_file_type = unserialize($setting->digital_file_type);
		if(empty($digital_file_type))
			$digital_file_type = array();
		$form_custom_field = unserialize($setting->form_custom_field);
		if(empty($form_custom_field))
			$form_custom_field = array();	
		if($setting->digital_global_file_type == 0 && ($setting->form_standard == 1 || ($setting->form_standard == 0 && in_array('cat_id', $form_custom_field)))) {
			$digital_file_type = unserialize($this->digital->category->cat_file_type);	
			if(empty($digital_file_type))
				$digital_file_type = array();
		}
		
		if(parent::beforeValidate()) {
			if($this->isNewRecord)
				$this->creation_id = Yii::app()->user->id;
			else
				$this->modified_id = Yii::app()->user->id;
			
			$digital_filename = CUploadedFile::getInstance($this, 'digital_filename');
			if($digital_filename != null) {
				$extension = pathinfo($digital_filename->name, PATHINFO_EXTENSION);
				if(!in_array(strtolower($extension), $digital_file_type))
					$this->addError('digital_filename', Yii::t('phrase', 'The file {name} cannot be uploaded. Only files with these extensions are allowed: {extensions}.', array(
						'{name}'=>$digital_filename->name,
						'{extensions}'=>Utility::formatFileType($digital_file_type, false),
					)));
			} else {
				if($this->isNewRecord && $controller == 'o/file')
					$this->addError('digital_filename', 'File cannot be blank.');				
			}
		}
		return true;
	}
	
	/**
	 * before save attributes
	 */
	protected function beforeSave() {
		$currentAction = strtolower(Yii::app()->controller->id.'/'.Yii::app()->controller->action->id);
		$setting = DigitalSetting::model()->findByPk(1, array(
			'select' => 'digital_path, cover_view_size, cover_file_type',
		));
		$cover_view_size = unserialize($setting->cover_view_size);
		$cover_file_type = unserialize($setting->cover_file_type);
		
		if(parent::beforeSave()) {
			$digital_path = $this->digital->digital_path;
			if($digital_path == '') {
				$pathUnique = Digitals::getUniqueDirectory($this->digital_id, $this->digital->salt, $this->digital->view->md5path);
				if($setting != null)
					$digital_path = $setting->digital_path.'/'.$pathUnique;
				else
					$digital_path = YiiBase::getPathOfAlias('webroot.public.digital').'/'.$pathUnique;
			}
	
			if(!file_exists($digital_path)) {
				@mkdir($digital_path, 0755, true);

				// Add file in directory (index.php)
				$newFile = $digital_path.'/index.php';
				$FileHandle = fopen($newFile, 'w');
			} else 
				@chmod($digital_path, 0755, true);
			
			if(in_array($currentAction, array('o/file/add','o/file/edit'))) {
				$this->digital_filename = CUploadedFile::getInstance($this, 'digital_filename');
				if($this->digital_filename != null) {
					if($this->digital_filename instanceOf CUploadedFile) {
						$fileName = time().'_'.$this->digital_id.'_'.$this->urlTitle($this->digital->digital_title).'.'.strtolower($this->digital_filename->extensionName);
						if($this->digital_filename->saveAs($digital_path.'/'.$fileName)) {
							if(!$this->isNewRecord) {
								if($this->old_digital_filename_input != '' && file_exists($digital_path.'/'.$this->old_digital_filename_input)) {
									rename($digital_path.'/'.$this->old_digital_filename_input, 'public/digital/verwijderen/'.$this->digital_id.'_'.$this->old_digital_filename_input);
									
									$extension = pathinfo($model->old_digital_filename_input, PATHINFO_EXTENSION);									
									if(in_array(strtolower($extension), $cover_file_type)) {
										foreach($cover_view_size as $key => $val) {
											unlink($digital_path.'/'.$key.'_'.$model->old_digital_filename_input);
										}
									}
								}
							}
							$this->digital_filename = $fileName;
						}
					}
				} else {
					if(!$this->isNewRecord && $this->digital_filename == '')
						$this->digital_filename = $this->old_digital_filename_input;
				}
			}
		}
		return true;
	}

	/**
	 * After delete attributes
	 */
	protected function afterDelete() {
		parent::afterDelete();
		
		$setting = DigitalSetting::model()->findByPk(1, array(
			'select' => 'digital_path, cover_view_size, cover_file_type',
		));
		$cover_view_size = unserialize($setting->cover_view_size);
		$cover_file_type = unserialize($setting->cover_file_type);
		
		$digital_path = $this->digital->digital_path;
		if($digital_path == '') {
			$pathUnique = Digitals::getUniqueDirectory($this->digital_id, $this->digital->salt, $this->digital->view->md5path);
			if($setting != null)
				$digital_path = $setting->digital_path.'/'.$pathUnique;
			else
				$digital_path = YiiBase::getPathOfAlias('webroot.public.digital').'/'.$pathUnique;
		}
		
		if($this->digital_filename != '' && file_exists($digital_path.'/'.$this->digital_filename)) {
			rename($digital_path.'/'.$this->digital_filename, 'public/digital/verwijderen/'.$this->digital_id.'_'.$this->digital_filename);	
			
			$extension = pathinfo($model->digital_filename, PATHINFO_EXTENSION);
			if(in_array(strtolower($extension), $cover_file_type)) {
				foreach($cover_view_size as $key => $val) {
					unlink($digital_path.'/'.$key.'_'.$model->digital_filename);
				}
			}
		}
	}
}