<p class="formselect formlabel-<?php echo $this->getName() ?>" id="<?php echo $this->getHTMLId() ?>">
    <label class="select <?php echo $this->getWarningClass() ?>" for="<?php echo $this->getFieldId() ?>"><?php echo $this->getLabel() ?></label>
    <select class="select <?php echo $this->getWarningClass() ?>" id="<?php echo $this->getFieldId() ?>" <?php echo $multiple ? 'name="' . $this->getFieldName() . '[]" multiple="multiple"' : 'name="' . $this->getFieldName() . '"' ?>  size="<?php echo $size ?>">
        <?php
        $optgroup_open = false;
        foreach ($options as $key => $value) {
          if($value === 'OPTGROUP') {
            if($optgroup_open) {
              echo '</optgroup>'.PHP_EOL.'<optgroup label="'.htmlspecialchars($key).'">'.PHP_EOL;
            } else {
              echo '<optgroup label="'.htmlspecialchars($key).'">'.PHP_EOL;
              $optgroup_open = true;
            }
          } else {
            echo '<option value="'.htmlspecialchars($key).'" ' . ( in_array($key, $this->getValue()) ? ' selected="selected"' : '' ) . '>'.$this->getLabelStyle($value).'</option>'.PHP_EOL;
          }
        }
        echo $optgroup_open ? '</optgroup>' : '';
        ?>
    </select>
</p>
