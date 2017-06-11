(function(){ 

  var $console = $('#console'),
      $generateBtn = $('#generateBtn'),
      $errors = 0;

  $('#generateBtn').bind("click", function(e) {

       var cmdPath = $(this).data('cmd-path'),
           consolePath = $(this).data('console-path');

       $.ajax({
            url: cmdPath, 
            success: function(commands){ 
                 if (commands.length) {
                      $errors = 0;
                      $generateBtn.prop('disabled', true);
                      $console.show();
                      runCommand(0, consolePath, commands);
                 }
            },
            error: function (request, status, error) {
               throw new Error(request.responseText);
           }
       });
  });

  var runCommand = function($i, url, commands) {
       $console.append('<div class="cmd">$ ' + commands[$i] + '</div>');
       $console.animate({scrollTop: $console.prop("scrollHeight")}, 1);

       var baseCmd = (commands[$i].indexOf('yii') === 0 || commands[$i].indexOf('yii') === 3) 
                     ? commands[$i].replace(/^yii ?/, '') 
                     : commands[$i];

       try {
         $.ajax({
            url: url,
            cache:  false,
            type : 'post',
            //dataType: 'json',
            data: {cmd: baseCmd},
            success: function(response){
               if (response) {
                 if (~response.indexOf('Stack trace:')) $errors++;
                 $console.append('<div class="response">' + response + '</div>');
                 $console.animate({scrollTop: $console.prop("scrollHeight")}, 800);
                 // call another ajax request to execute the next command.
                 $i++;
                 if ($i < commands.length) {
                    (commands[$i].indexOf('migrate/create') < 0) ? runCommand($i, url, commands) : setTimeout(function() {
                        /*
                         * forcing 1 sec waiting if cmd is 'migrate/create' to avoid migrations having the same version number and lose 
                         * correct order as php's time() function in yii\console\controllers\MigrateController is used to generate their ID's.
                         */
                        runCommand($i, url, commands);
                      }, 1000);
                 }
                 else { 
                      $generateBtn.prop('disabled', false);
                      ($errors > 0)
                        ? $console.append('<div class="error"><span class="fui-cross"></span> '+$errors+' Error(s) thrown while running shell commands</div>')
                        : $console.append('<div class="success"><span class="fui-check"></span> DONE</div>');
                 }
               }
            },
            error: function (request, status, error) {
                $generateBtn.prop('disabled', false);
                throw new Error(request.responseText);
           }
         });
       } catch(e) { 
            $console.append('<div class="error"><span class="fui-alert-circle"></span> Failed to run the command for unknown reason</div>');
            $generateBtn.prop('disabled', false);
       }
  }

     
})();