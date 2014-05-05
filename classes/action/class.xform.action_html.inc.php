<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_action_html extends rex_xform_action_abstract
{

    function execute()
    {

        $html = $this->getElement(2);
        echo $html;

        return true;
    }

    function getDescription()
    {
        return 'action|html|&lt;b&gt;fett&lt;/b&gt;';
    }

}
