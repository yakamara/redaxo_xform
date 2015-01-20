<?php


    $type = isset($type) ? $type : 'text';
    $class = $type == 'text' ? '' : 'form-' . $type . ' ';
    $value = isset($value) ? $value : stripslashes($this->getValue());

    $notice = $this->getElement('notice') != '' ? '<p class="help-block">' . $this->getElement('notice') . '</p>' : '';
    $class .= $this->getElement('required') ? 'form-is-required ' : '';

    $class_group   = trim('form-group ' . $class . $this->getElement(5) . ' ' . $this->getWarningClass());
    $class_control = trim('form-control');
?>
<div class="<?php echo $class_group ?>" id="<?php echo $this->getHTMLId() ?>">
    <label class="control-label" for="<?php echo $this->getFieldId() ?>"><?php echo $this->getLabel() ?></label>
    <input class="<?php echo $class_control ?>" type="<?php echo $type ?>" name="<?php echo $this->getFieldName() ?>" id="<?php echo $this->getFieldId() ?>" value="<?php echo htmlspecialchars($value) ?>"<?php echo $this->getAttributeElement('placeholder'), $this->getAttributeElement('autocomplete'), $this->getAttributeElement('pattern'), $this->getAttributeElement('required', true), $this->getAttributeElement('disabled', true), $this->getAttributeElement('readonly', true) ?> />
    <?php echo $notice ?>
</div>
