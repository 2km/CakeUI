<!DOCTYPE html>
<html lang="en">
  <head>
    <?php echo $this->Html->charset(); ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
    <?php echo __('CakeUI:'); ?>
    <?php echo $title_for_layout; ?>
    </title>
    <?php
      echo $this->Html->meta('icon');
      echo $this->Html->css(array(
        '/CakeUI/css/bootstrap/bootstrap.min',
        '/CakeUI/css/bootstrap/bootstrap-theme.min',
        '/CakeUI/css/bootstrap/theme'
      ));
      echo $this->fetch('meta');
      echo $this->fetch('css');
    ?>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body role="document">
    <div class="container theme-showcase" role="main">
        <?php echo $this->Session->flash(); ?>
        <?php echo $this->fetch('content'); ?>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <?php
      echo $this->Html->script(array(
        '/CakeUI/js/bootstrap/bootstrap.min',
        '/CakeUI/js/inputmask/min/jquery.inputmask',
        '/CakeUI/js/inputmask/min/jquery.inputmask.numeric.extensions',
        '/CakeUI/js/maskmoney/jquery.maskMoney.min',
        '/CakeUI/js/cakeui',
      ));
      echo $this->fetch('script');
    ?>
  </body>
  <script>
    $('.cakeui-tooltip').tooltip();
    $('.pop').popover({
      container: 'body',
      html: true,
      content: function () {
        return $(this).next('.pop-content').html();
      }
    });
    </script>
</html>