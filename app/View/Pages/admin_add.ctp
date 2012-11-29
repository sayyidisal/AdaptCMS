<?php
	$this->EditArea->editor('PageContent');
?>

<script>
 $(document).ready(function(){
    $("#PageAdminAddForm").validate();
 });
 </script>

<h1>Add Page</h1>

<?php
	echo $this->Form->create('Page', array('class' => 'well'));

	echo $this->Form->input('title', array('type' => 'text', 'class' => 'required'));
	echo $this->Form->input('content', array('style' => 'width:80%;height: 300px', 'class' => 'required'));

	echo $this->Form->hidden('created', array('value' => $this->Time->format('Y-m-d H:i:s', time())));
?>
<br />
<?= $this->Form->end(array(
		'label' => 'Submit',
		'class' => 'btn'
)) ?>