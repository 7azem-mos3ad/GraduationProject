<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>{x0x}</title>
  <link rel='stylesheet' href='/engien/error/flexgrid.min.css'><link rel="stylesheet" href="/engien/error/style.css">

</head>
<body>
<div class="container">
    <div class="row">
        <div class="xs-12 md-6 mx-auto">
            <div id="countUp">
                <div class="number" data-count="<?php echo $ErrorDocument['number'];?>">0</div>
                <div class="text"><?php echo $ErrorDocument['message'];?></div>
                <div class="text">This may not mean anything.</div>
                <div class="text">I'm probably working on something that has blown up.</div>
            </div>
        </div>
    </div>
</div>
<script src='/engien/static/js/jquery.js'></script>
<script src='/engien/error/in-view.js'></script><script  src="/engien/error/script.js"></script>

</body>
</html>
