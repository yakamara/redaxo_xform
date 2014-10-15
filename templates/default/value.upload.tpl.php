<p class="<?php echo $this->getHTMLClass() ?> formlabel-<?php echo $this->getName() ?>" id="<?php echo $this->getHTMLId() ?>">
    <label class="text <?php echo $this->getWarningClass() ?>" for="<?php echo $this->getFieldId() ?>" >
        <?php echo $this->getLabel() ?>
    </label>
    <input class="upload <?php echo $this->getWarningClass() ?>" id="<?php echo $this->getFieldId() ?>" name="file_<?php echo md5($this->getFieldName('file')) ?>" type="file" />
</p>
