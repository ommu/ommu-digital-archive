<?php
/**
 * Digitals (digitals)
 * @var $this AdminController
 * @var $model Digitals
 * version: 0.0.1
 *
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @copyright Copyright (c) 2016 Ommu Platform (ommu.co)
 * @created date 20 October 2016, 10:14 WIB
 * @link http://company.ommu.co
 * @contect (+62)856-299-4114
 *
 */
?>

<?php 
$count = count($tags);
$i=0;
foreach($tags as $key => $val) {
	$i++;
	if($count != $i) {?>
		<a href="<?php echo Yii::app()->controller->createUrl('o/tags/manage',array('tag'=>$val->tag_id));?>" title="<?php echo $val->tag->body?>"><?php echo $val->tag->body?></a>, 
	<?php } else {?>
		<a href="<?php echo Yii::app()->controller->createUrl('o/tags/manage',array('tag'=>$val->tag_id));?>" title="<?php echo $val->tag->body?>"><?php echo $val->tag->body?></a>	
<?php }
}?>