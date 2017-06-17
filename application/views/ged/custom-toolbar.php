<link rel="stylesheet" href="<?php echo base_url();?>assets/css/jquery.fancybox.css" type="text/css" media="screen" />
<style>
  .nav-pills-custom{
    float: right;
    margin-top: 0px;
  }
  .nav-pills-custom span{
    line-height: 18px
  }
  .nav-pills-custom li a{
    padding: 3px 8px;
  }
  .toolbar-container{
    border: 1px solid grey;
    padding: 3px
  }
  .context-title{
    text-align: center;
    color: \#337ab7;
    font-size: 15px;
  }
  .toolbar-container .form-group{
    margin-top: 0px;
    margin-bottom: 5px;
    font-size: 11px;
    float: left;
  }
  .toolbar-container .form-group label{
    float: left;
    margin-top: 7px;
    margin-right: 5px;
  }
  .toolbar-container .form-group .btn{
    float: left;
    margin-left: -5px;
    height: 30px;
  }
  .toolbar-container .form-group select{
    width: 160px;
    float: left;
    margin-right: 10px;
  }
  .toolbar-container .form-group input{
    width: 70%;
    float: left;
  }
  #sel_action_all,
  #sel_view{
    width: 175px;
  }
</style>

<?php

$document_type_filter = $this->uri->segment(3);

$document_types = array(
  array('value' => "Infographie", 'label' => "Infographie"),
  array('value' => "Plan", 'label' => "Plan"),
  array('value' => "Piece-Comptable", 'label' => "Piece Comptable"),
  array('value' => "Facture-Client", 'label' => "Facture Client"),
  array('value' => "Devis-Client", 'label' => "Devis Client"),
  array('value' => "E-Mailing", 'label' => "E-Mailing"),
  array('value' => "Site-Internet", 'label' => "Site Internet"),
  array('value' => "Administratif-Divers", 'label' => "Administratif Divers"),
  array('value' => "Developpement", 'label' => "Developpement"),
  array('value' => "Ressources-Humaines", 'label' => "Ressources Humaines"),
  array('value' => "Commercial", 'label' => "Commercial"),
);

?>

<div class="toolbar-container">
  <div class="form-group">
  	<label for="action">Document Type</label>
    <select class="form-control input-sm" id="filter-document-type">      
      <option value="">All</option> 
      <?php 
        foreach($document_types as $row): 
            if($row['value'] == $document_type_filter):
      ?>
        <option selected value="<?php echo $row['value'];?>"><?php echo $row['label'];?></option>
      <?php else: ?>
        <option value="<?php echo $row['value'];?>"><?php echo $row['label'];?></option>
      <?php endif; 
        endforeach; ?>
    </select>
  </div>
  <div style="clear: both"></div>
</div>