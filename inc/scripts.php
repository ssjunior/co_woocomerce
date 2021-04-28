<?php

add_action('wp_head', function () {
    echo "
        <script>window.dataLayer = window.dataLayer || []</script>
        <script src=\"https://integracao.events.pipz.io/js?id=1706.3848b1f5\"></script>
    ";
});
