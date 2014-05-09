<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_action_abstract extends rex_xform_base_abstract
{
    var $action = array();

    function execute()
    {
        return false;
    }

}
