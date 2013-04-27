<?php

/**
* XForm
* @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
* @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_article extends rex_xform_abstract
{

  function enterObject()
  {
    $artikel = new rex_article;
    if ( ($artikel->setArticleId($this->getElement(1)) )) {
      $this->params["form_output"][$this->getId()] = '<div class="article" id="'.$this->getHTMLId().'">' . $artikel->getArticle() . '</div>';
    }
  }

  function getDescription()
  {
    return "article -> Beispiel: article|article_id";
  }

}