<?php
function local_cts_co_extend_navigation(global_navigation $navigation) {
    if ($home = $navigation->find('home', global_navigation::TYPE_SYSTEM)) {
        $home->remove();
    }
}