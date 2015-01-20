<?php
    $notice = $this->getElement('notice') != '' ? '<p class="help-block">' . $this->getElement('notice') . '</p>' : '';
    $class = $this->getElement('required') ? 'form-is-required ' : '';

    $class_group   = trim('form-group ' . $class . $this->getElement(5) . ' ' . $this->getWarningClass());
    $class_control = trim('form-control');
?>
<div class="<?php echo $class_group ?>" id="<?php echo $this->getHTMLId() ?>">
    <label class="control-label" for="<?php echo $this->getFieldId() ?>"><?php echo $this->getLabel() ?></label>
    <textarea class="<?php echo $class_control ?>" name="<?php echo $this->getFieldName() ?>" id="<?php echo $this->getFieldId() ?>" rows="10" <?php echo $this->getAttributeElement('placeholder'), $this->getAttributeElement('pattern'), $this->getAttributeElement('required', true), $this->getAttributeElement('disabled', true), $this->getAttributeElement('readonly', true) ?>><?php echo htmlspecialchars(stripslashes($this->getValue())) ?></textarea>
    <?php echo $notice ?>
</div>
