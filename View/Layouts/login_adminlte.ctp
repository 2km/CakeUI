<!DOCTYPE html>
<html class="lockscreen">
    <head>
        <?php echo $this->Html->charset(); ?>
        <title>
            <?php echo __('CakeUI:'); ?>
            <?php echo $title_for_layout; ?>
        </title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php
            echo $this->Html->meta('icon');
            echo $this->Html->css(array(
                '/CakeUI/css/bootstrap/bootstrap',
                '/Dkmadmin/css/font-awesome.min',
                '/Dkmadmin/css/ionicons.min',
                '/Dkmadmin/css/AdminLTE',
                '/CakeUI/css/bootstrap/theme',
                '/Dkmadmin/css/iCheck/minimal/minimal'
            ));
            echo $this->fetch('meta');
            echo $this->fetch('css');
        ?>
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <?php 
          echo $this->Session->flash(); 
          echo $this->Session->flash('auth',array('element'=>'Dkmadmin.authError'));
          echo $this->fetch('content');
        ?>


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
                '/Dkmadmin/js/AdminLTE/iCheck/icheck.min',
                '/Dkmadmin/js/AdminLTE/app'
            ));
            echo $this->fetch('script');
        ?>
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

    </body>
</html>