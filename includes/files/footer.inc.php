    
    </main>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="dist/js/jquery-3.4.1.min.js"></script>
    <script src="dist/js/jquery-ui.min.js"></script>    
    <script src="dist/js/popper.min.js"></script>
    <script src="dist/js/bootstrap.min.js"></script>
    <script src="dist/js/jquery.blockUI.js"></script>

    <?php 
      $f = $_SERVER['SCRIPT_FILENAME'];
      $dirname = dirname($f);
      $f = str_replace($dirname,'',$f);
      $f = str_replace('.php','-footer.inc.php',$f);

      $f = $dirname.'/includes/files'.$f;
      if(file_exists($f)){
        include($f);
      }
    ?>
    
  </body>
</html>