<?php

    $notice = [];
    if ($this->getElement('notice') != "") {
      $notice[] = $this->getElement('notice');
    }
    if (isset($this->params['warning_messages'][$this->getId()]) && !$this->params['hide_field_warning_messages']) {
        $notice[] =  '<span class="text-warning">' . rex_translate($this->params['warning_messages'][$this->getId()], null, false) . '</span>'; //    var_dump();
    }
    if (count($notice) > 0) {
        $notice = '<p class="help-block">' . implode("<br />", $notice) . '</p>';
    
    } else {
        $notice = '';
    }

    $class = $this->getElement('required') ? 'form-is-required ' : '';

    $class_group   = trim('form-group ' . $class . $this->getElement(5) . ' ' . $this->getWarningClass());
    $class_control = trim('form-control');
?>
<div class="<?php echo $class_group ?>" id="<?php echo $this->getHTMLId() ?>">
    <label class="control-label" for="<?php echo $this->getFieldId() ?>"><?php echo $this->getLabel() ?></label>
    <textarea class="<?php echo $class_control ?>" name="<?php echo $this->getFieldName() ?>" id="<?php echo $this->getFieldId() ?>" rows="10" <?php echo $this->getAttributeElement('placeholder'), $this->getAttributeElement('pattern'), $this->getAttributeElement('required', true), $this->getAttributeElement('disabled', true), $this->getAttributeElement('readonly', true) ?>><?php echo htmlspecialchars(stripslashes($this->getValue())) ?></textarea>
    <?php echo $notice ?>
</div>
