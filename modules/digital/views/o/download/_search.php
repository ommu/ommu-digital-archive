<?php
/**
 * Digital Downloads (digital-downloads)
 * @var $this DownloadController
 * @var $model DigitalDownloads
 * @var $form CActiveForm
 * version: 0.0.1
 *
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @copyright Copyright (c) 2017 Ommu Platform (ommu.co)
 * @created date 8 January 2017, 20:54 WIB
 * @link http://company.ommu.co
 * @contect (+62)856-299-4114
 *
 */
?>

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
	<ul>
		<li>
			<?php echo $model->getAttributeLabel('download_id'); ?><br/>
			<?php echo $form->textField($model,'download_id',array('size'=>11,'maxlength'=>11)); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('frontend'); ?><br/>
			<?php echo $form->textField($model,'frontend'); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('file_id'); ?><br/>
			<?php echo $form->textField($model,'file_id',array('size'=>11,'maxlength'=>11)); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('user_id'); ?><br/>
			<?php echo $form->textField($model,'user_id',array('size'=>11,'maxlength'=>11)); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('downloads'); ?><br/>
			<?php echo $form->textField($model,'downloads'); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('download_date'); ?><br/>
			<?php echo $form->textField($model,'download_date'); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('download_ip'); ?><br/>
			<?php echo $form->textField($model,'download_ip',array('size'=>20,'maxlength'=>20)); ?>
		</li>

		<li class="submit">
			<?php echo CHtml::submitButton(Yii::t('phrase', 'Search')); ?>
		</li>
	</ul>
<?php $this->endWidget(); ?>