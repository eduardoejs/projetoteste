<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 08/01/15
 * Time: 17:00
 */

namespace Code\Sistema\Helper;


interface PaginatorInterface
{

    public function __construct($page, $limit, $num_reg, $url_base, array $variables);

    public function createLinks();

    public function setVariables(array $variables);

} 