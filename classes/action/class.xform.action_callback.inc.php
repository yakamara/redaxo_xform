<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_action_callback extends rex_xform_action_abstract
{

    function execute()
    {

        if (!$this->getElement(2)) {
            return false;
        }

        $f = $this->getElement(2);

        if (strpos($f, '::') !== false) {
            $f = explode('::', $f, 2);
            if (is_callable($f[0], $f[1])) {
                call_user_func($f, $this);
            }
        } elseif (function_exists($f)) {
            $f($this);
        }

        return;

    }

    function getDescription()
    {
        return 'action|callback|mycallback / myclass::mycallback';
    }

}
