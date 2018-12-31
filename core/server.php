<?php
  $requestURI = explode('?', $_SERVER['REQUEST_URI'], 2);
  $requestURI = explode('/', $requestURI[0]);
  $URIfilter = array_filter($requestURI, function ($a){
      return $a;
  });
  $hostname = $_SERVER['HTTP_HOST'];

  if(count($URIfilter) < 1 || $URIfilter[1] != 'api'){
      echo json_encode("endpoint not found!");
      http_response_code('404');
  }
  else{
      if(count($URIfilter) > 1){
          array_shift($URIfilter);
          switch ($URIfilter[0]){
              case 'about':
                  require $_SERVER['DOCUMENT_ROOT'].'/'.'applications/about/url.php';
                  new AboutURL($URIfilter);
                  break;
              case 'blog':
                  require $_SERVER['DOCUMENT_ROOT'].'/'.'applications/blog/url.php';
                  new BlogURL($URIfilter);
                  break;
              case 'skillset':
                  require $_SERVER['DOCUMENT_ROOT'].'/'.'applications/skillset/url.php';
                  new SkillSetURL($URIfilter);
                  break;
              case 'service':
                  require $_SERVER['DOCUMENT_ROOT'].'/'.'applications/service/url.php';
                  new ServiceURL($URIfilter);
                  break;
              case 'portfolio':
                  require $_SERVER['DOCUMENT_ROOT'].'/'.'applications/portfolio/url.php';
                  new PortfolioURL($URIfilter);
                  break;
              case 'page-setting':
                  require $_SERVER['DOCUMENT_ROOT'].'/'.'applications/pagesetting/url.php';
                  new PageSettingURL($URIfilter);
                  break;
              case 'user':
                  require $_SERVER['DOCUMENT_ROOT'].'/'.'applications/user/url.php';
                  new UserURL($URIfilter);
                  break;
              case 'store':
                  require $_SERVER['DOCUMENT_ROOT'].'/'.'applications/store/url.php';
                  new StoreURL($URIfilter);
                  break;
              case 'team':
                  require $_SERVER['DOCUMENT_ROOT'].'/'.'applications/team/url.php';
                  new TeamURL($URIfilter);
                  break;
              case 'make-enquiry':
                  require $_SERVER['DOCUMENT_ROOT'].'/'.'applications/enquiry/url.php';
                  new EnquiryURL($URIfilter);
                  break;
              case 'course':
                  require $_SERVER['DOCUMENT_ROOT'].'/'.'applications/course/url.php';
                  new CourseURL($URIfilter);
                  break;
              case 'message-board':
                  require $_SERVER['DOCUMENT_ROOT'].'/'.'applications/messageBoard/url.php';
                  new MessageURL($URIfilter);
                  break;
              case 'auth':
                  require $_SERVER['DOCUMENT_ROOT'].'/'.'Logics/auth/url.php';
                  new AuthURL($URIfilter);
                  break;
              case 'run-migration':
                  require $_SERVER['DOCUMENT_ROOT'].'/'.'migrations/runmigrations.php';
                  new RunMigration($URIfilter);
                  break;
              case 'controls':
                  require $_SERVER['DOCUMENT_ROOT'].'/'.'Logics/controls/url.php';
                  new ControlURL($URIfilter);
                  break;
              case 'password-change':
                  require_once $_SERVER['DOCUMENT_ROOT'].'/'.'/applications/passwordreset/url.php';
                  new PasswordResetURL($URIfilter);
                  break;
              case 'cke':
                  require $_SERVER['DOCUMENT_ROOT'].'/'.'extras/ckeControl.php';
                  new CKEController();
                  break;

              default:
                  echo json_encode("endpoint not found!");
                  http_response_code('404');
          }
      }
      else{
          require $_SERVER['DOCUMENT_ROOT'].'/'.'resources/home.php';
      }
  }

?>
