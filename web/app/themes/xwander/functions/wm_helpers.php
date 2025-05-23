<?php 

function wm_strip_tags_content($text, $tags = '', $invert = FALSE) 
{

    preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
    $tags = array_unique($tags[1]);

    if(is_array($tags) AND count($tags) > 0) 
    {
        if($invert == FALSE) 
        {
          return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
        }
        else 
        {
          return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text);
        }
    }
    elseif($invert == FALSE) 
    {
        return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
    }

    return $text;
}



function wm_is_gutenberg_page() 
{

    if (is_admin() &&  function_exists( 'get_current_screen' ) ) 
    {
        $screen = get_current_screen();
        return $screen->is_block_editor;
    }
    return FALSE;

}

function wm_replace_button_text($str, $replacement) 
{
    return preg_replace('#(<button.*?>).*?(</button>)#', '$1'.$replacement.'$2' , $str);
}

