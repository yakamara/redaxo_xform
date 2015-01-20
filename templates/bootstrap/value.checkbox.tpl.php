<?php
    $value = isset($value) ? $value : 1;
    
    $class_group = trim('form-group ' . $this->getWarningClass());
?>
<div class="checkbox" id="<?php echo $this->getHTMLId() ?>">
    <label>
        <input type="checkbox" id="<?php echo $this->getFieldId() ?>" name="<?php echo $this->getFieldName() ?>" value="<?php echo $value ?>"<?php echo $this->getValue() == $value ? ' checked="checked"' : '' ?> />
        <?php echo $this->getLabel() ?>
    </label>
</div>