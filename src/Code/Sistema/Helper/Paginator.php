<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 08/01/15
 * Time: 16:59
 */

namespace Code\Sistema\Helper;


class Paginator implements PaginatorInterface
{
    private $page;
    private $limit;
    private $num_reg;
    private $url_base;
    private $next;
    private $prev;
    private $first;
    private $last;
    private $tag_open = '<li>';
    private $tag_open_disable = '<li class="disabled">';
    private $tag_open_active = '<li class="active">';
    private $tag_close = "</li>\n";

    private $urlVariables;

    public function __construct($page, $limit, $num_reg, $url_base, array $variables = array())
    {
        $this->page = $page;
        $this->limit = $limit;
        $this->num_reg = $num_reg;
        $this->url_base = $url_base;
        $this->next = ( $page >= 1 ) ? $page + 1 : $page + 2;
        $this->prev = $page - 1;
        $this->first = 1;
        $this->last = ($num_reg > $limit) ? ceil($num_reg / $limit) : 1;

        $this->setVariables($variables);
    }

    public function createLinks()
    {
        $links = "";
        if($this->num_reg > $this->limit){
            $aux_url = (strpos($this->url_base, '?') != false) ? '&' : '?';

            if ( $this->page > 1) {
                $links.= $this->tag_open .'<a href="'. $this->url_base . '/' . $this->first . $this->urlVariables .'" title="Inicio"><<</a>'. $this->tag_close;
                $links.= $this->tag_open .'<a href="'. $this->url_base . '/' . $this->prev . $this->urlVariables .'" title="Voltar"><</a>'. $this->tag_close;
            } else {
                $links.= $this->tag_open_disable .'<a href="#"><<</a>'. $this->tag_close;
                $links.= $this->tag_open_disable .'<a href="#"><</a>'. $this->tag_close;
            }

            for( $i_pg= 1 ; $i_pg <= $this->last ; $i_pg++ ) {
                if ( $this->page == $i_pg or ($this->page == null and $i_pg == 1) ) {
                    $links.= $this->tag_open_active . '<a href="#">' . $i_pg . '</a>' . $this->tag_close;
                } else {
                    $links.= $this->tag_open .'<a href="'. $this->url_base . '/' . $i_pg . $this->urlVariables .'">'.$i_pg.'</a>'. $this->tag_close;
                }
            }

            if ($this->page < $this->last) {
                $links.= $this->tag_open .'<a href="'. $this->url_base . '/' . $this->next . $this->urlVariables .'" title="Avançar">></a>'. $this->tag_close;
                $links.= $this->tag_open .'<a href="'. $this->url_base . '/' . $this->last . $this->urlVariables .'" title="Final">>></a>'. $this->tag_close;
            } else {
                $links.= $this->tag_open_disable .'<a href="#">></a>'. $this->tag_close;
                $links.= $this->tag_open_disable .'<a href="#">>></a>'. $this->tag_close;
            }
        }
        return $links;
    }

    public function setVariables(array $variables)
    {
        $i = 0;
        foreach($variables as $name => $value){
            if($i == 0)
                $this->urlVariables.= "?{$name}={$value}";
            else
                $this->urlVariables.= "&{$name}={$value}";
        }
    }

} 