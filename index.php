<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="keywords"           content="入群答题" />
    <meta name="description"        content="这里是进入某一个神秘的群必须要回答的问题" />
 
    <meta name="viewport"           content="width=320,user-scalable=0,initial-scale=1.0,maximum-scale=1.0, minimum-scale=1.0" />
    <title>入群答题</title>
 
    <link type="text/css" rel="stylesheet" href="/static/css/bootstrap.min.css" /> 
    <script src="/static/js/clipboard.min.js"></script>
    <script src="/static/js/bootstrap.min.js"></script>
</head>
<body>
    <!-- 这里是大标题 -->
    <div class="jumbotron">
      <h1>入群试题</h1> 
      <p>通过这里的测试，即可获得入群口令!</p>
      <p>这里涵盖了各种有趣的题，即使不会的问题也没有关系，智能小老师会按照年级给出判断。</p> 
    </div>
    <?php
        $msg = "";
        $show_msg = false;
        $msg_is_success = true;

        $json_string = file_get_contents('y0u_w1ll_never_9ue5t.json');  
        $data = json_decode($json_string, true);

        function search_question($name) {
            global $data;
            foreach($data["questions"] as $q) {
                if($name === $q["name"]) {
                    return $q;
                }
            }
            return NULL;
        }

        function check_answer($name,$anw) {
            $q = $name;
            if(in_array($anw, $q["answer"])) {
                return 1;
            } else {
                return 0;
            }
        }
        
        function check_test($grade) {
            if($grade < 0 || $grade > 3) {
                show_msg("非法的年级信息",0);
                return;
            }
            global $data;

            $questions = $data["option"][$grade];
            
            $total = count($questions);
            $all = count($data["questions"]);

            $g1 = 80 / $total;
            $g2 = 40 / ($all - $total);
            $c = 0;

            foreach ($data["questions"] as $q) {
                if(isset($_POST[$q["name"]])) {
                    $ans = $_POST[$q["name"]];
                } else {
                    $ans = "";
                }
                $ret = check_answer($q, $ans);
                if($ret == 1) {
                    if(in_array($q["name"], $questions)) {
                        $c += $g1;
                    } else {
                        $c += $g2;
                    }

                } else if ($ret == -1) {
                    show_msg("非法的题目（请联系管理员，也请不要想渗透）",0);
                    return;
                }
            }
            get_result($c);
        }

        function flag_gen($c, $flag) {
            return $flag . "_" . $c;
        }

        function get_result($c) {
            global $data;
            if($c >= 100) {
                $c = 100;
            }
            
            if( $c >= 60) {
                $flag = $data["password"];
                $msg = "得分：".intval($c)." .恭喜，请将下面的口令输入在群验证的答案处。";
                $msg .= "<div class=\"input-group\">";
                $msg .= "<input id=\"foo\" value=\"". $flag ."\">\n";
                $msg .= "<div class=\"input-group-append\">\n";
                $msg .= "<button class=\"ccc btn btn-outline-success\" data-clipboard-target=\"#foo\" >点击复制</button></div>\n";
                $msg .= "<script> var clipboard = new ClipboardJS('.ccc'); </script>\n";
                $msg .= "</div>";
                show_msg($msg, 1);
            } else {
                $msg = "得分：".intval($c)." .非常遗憾，没有通过测试";
                show_msg($msg, 0);
            }
        }

        function show_msg($msg,$type) {
            $msg_type = "";
            if($type === 1) {
                $msg_type = "alert-success";
            } else {
                $msg_type = "alert-danger";
            }
            echo "<div class=\"alert ". $msg_type ."\"> ". $msg ."</div>";
        }

        if(isset($_POST["grade"])) {
            check_test($_POST["grade"]);
        }
    ?>

    <div class="container">
        <h2>试卷</h2>
            <form action="/index.php" method="POST">
              <h4>基本信息</h4>
              <div class="form-group">
                <label for="email">年级</label>
                <select class="form-control" name="grade">
                  <option value="0">小学</option>
                  <option value="1">初中</option>
                  <option value="2">高中</option>
                  <option value="3">本科及以上</option>
                </select>
              </div>
              <h4>试卷正文</h4>
              <?php
                foreach($data["questions"] as $q) {
                    echo "<div class=\"form-group\">";
                    echo "<label for=\"" . $q["name"] . "\">". $q["title"] . "</label>";
                    echo "<input type=\"text\" class=\"form-control\" id=\"". $q["name"] ."\" name=\"".$q["name"]."\">";
                    echo "</div>";
                }
              ?>
              <button type="submit" class="btn btn-primary">提交</button>
            </form>
    </div>
    <footer class="footer navbar-fixed-bottom ">
    <div class="container">
        <p style="text-align:center">由 Ertuil 提供，Copyleft @ 2019</p>
    </div>
    </footer>
</body>
</html>
