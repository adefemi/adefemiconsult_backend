<?php
    class RunMigration{
        function __construct($URI)
        {
            $this->state = 1;
            $this->type = "";
            $this->url = $URI;
            $this->execute();
        }
        function execute(){
            if($this->verifyRequest() < 1){
                return;
            }
            switch ($this->type){
                case 'about':
                    require_once $_SERVER['DOCUMENT_ROOT'].'/'.'applications/about/model.php';
                    new AboutModel($this->state);
                    break;
                case 'blog':
                    require_once $_SERVER['DOCUMENT_ROOT'].'/'.'applications/blog/model.php';
                    new BlogModel($this->state);
                    break;
                case 'skillset':
                    require_once $_SERVER['DOCUMENT_ROOT'].'/'.'applications/skillset/model.php';
                    new SkillSetModel($this->state);
                    break;
                case 'service':
                    require_once $_SERVER['DOCUMENT_ROOT'].'/'.'applications/service/model.php';
                    new ServiceModel($this->state);
                    break;
                case 'portfolio':
                    require_once $_SERVER['DOCUMENT_ROOT'].'/'.'applications/portfolio/model.php';
                    new PortfolioModel($this->state);
                    break;
                case 'page-setting':
                    require $_SERVER['DOCUMENT_ROOT'].'/'.'applications/pagesetting/model.php';
                    new PageSettingModel($this->state);
                    break;
                case 'user':
                    require_once $_SERVER['DOCUMENT_ROOT'].'/'.'applications/user/model.php';
                    new UserModel($this->state);
                    break;
                case 'store':
                    require_once $_SERVER['DOCUMENT_ROOT'].'/'.'applications/store/model.php';
                    new StoreModel($this->state);
                    break;
                case 'team':
                    require_once $_SERVER['DOCUMENT_ROOT'].'/'.'applications/team/model.php';
                    new TeamModel($this->state);
                    break;
                case 'course':
                    require_once $_SERVER['DOCUMENT_ROOT'].'/'.'applications/course/model.php';
                    new CourseModel($this->state);
                    break;
                case 'message-board':
                    require_once $_SERVER['DOCUMENT_ROOT'].'/'.'applications/messageBoard/model.php';
                    new MessageModel($this->state);
                    break;
                default:
                    echo 'Migration not defined for '.ucfirst($this->type);
            }
        }

        function verifyRequest(){
            if(count($this->url)>1){
                echo json_encode(['error' => "endpoint not found!"]);
                http_response_code('404');
                return 0;
            }
            if(!isset($_SERVER['QUERY_STRING'])){
                echo "No query defined";
                return 0;
            }
            parse_str($_SERVER['QUERY_STRING'], $result);
            if(!isset($result['type'])){
                echo "type required";
                return 0;
            }
            if(isset($result['state'])){
                $this->state = $result['state'];
            }
            $this->type = $result['type'];

            return 1;
        }
    }

    ?>