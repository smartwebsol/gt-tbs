<?php 
$langLogin = $lang->get(['login']);

?>
<div class="row mt-5 login-box">
  <div class="col-sm-12"><h4><?php echo $langLogin['heading']?></h4></div>
  <div class="col-sm-12">

    <form method="post" action="<?php echo _URL?>forms/login.action.php">

      <div class="row">        

        <div class="col-sm-12">          

          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text"><?php echo $langLogin['username']?>: </span>
            </div>
            <input type="text" class="form-control" name="username" />
          </div>

        </div>

        <div class="col-sm-12">

          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text"><?php echo $langLogin['password']?>: </span>
            </div>
            <input type="password" class="form-control" name="password" />
          </div>

        </div>

        <div class="col-sm-12 text-right">
          <button type="submit" class="btn btn-pink" onclick="$('.login-box').block({message:'<?php echo $langLogin['wait']?>'})"><?php echo $langLogin['btnSubmit']?></button>
        </div>  

      </div>
    </form>
  </div>
</div>

<style>
  .login-box{
    width:400px; 
    margin: 0 auto;
    border: 1px Solid #ddd;
    border-radius: 10px;
    padding: 10px 2px;
    background-color:#f8f8f8;
  }

  @media only screen and (max-width: 600px) {
    .login-box{
      width:unset;
    }
  }
</style>  