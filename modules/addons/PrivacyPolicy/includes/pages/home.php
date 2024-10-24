<?php

use Illuminate\Database\Capsule\Manager as Capsule;

if (!defined("WHMCS")) die("This file cannot be accessed directly");

if($_REQUEST['a'] == '')
{
  if($_REQUEST['success'])
  {
    echo ' <script>$(document).ready( function(){ window.setTimeout( function(){ $(".alert").slideUp(); }, 2500);});</script>';
    echo '<div class="alert alert-success" style="font-size:14px"><strong><span class="fa fa-check"></span> '.$LANG['success'].'</strong> '.$LANG['successhelp'].'</div>';
  }

  echo '<form name="form" action="'.$modulelink.'&a=settings" method="POST" class="form-horizontal">
          <div class="panel panel-default">
            <div class="panel-heading">
              <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-check"></i> '.$LANG['savechanges'].'</button>
            </div>
            <div class="panel-body">
            
              <div class="form-group">
                <label class="col-md-3 control-label">'.$LANG['description'].'</label>
                <div class="col-md-6">
                  <input type="text" name="description" value="'.$SLSETTINGS['Description'].'" class="form-control"> 
                </div>
              </div>              <div class="form-group">
                <label class="col-md-3 control-label">'.$LANG['keywords'].'</label>
                <div class="col-md-6">
                  <input type="text" name="keywords" value="'.$SLSETTINGS['Keyword'].'" class="form-control"> 
                  <span class="help-block">'.$LANG['keywordshelp'].'</span>
                </div>
              </div>
            </div>
          </div>
        </form>';          

  echo '<p><a class="btn btn-primary" href="'.$modulelink.'&a=manage" role="button"><i class="fa fa-plus"></i> '.$LANG['createnewprivacypolicy'].'</a>
  <a class="btn btn-success" href="'.$CONFIG['SystemURL'].'/index.php?m=PrivacyPolicy" role="button" target="_blank"><i class="fa fa-globe"></i> '.$LANG['privacypolicypage'].'</a></p>';
  echo '<div class="panel panel-default">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>'.$LANG['title'].'</th>
                <th>'.$LANG['status'].'</th>
                <th>'.$LANG['sortorder'].'</th>
                <th></th>
              </tr>
            </thead>
            <tbody>';
  $result = Capsule::table('mod_privacypolicy')->where('language', '')->orderBy('orders', 'ASC')->get();
  foreach ($result as $data)
  {
    $id      = $data->id;
    $title   = $data->title;
    $orders  = $data->orders;
    $status  = $data->status;
       
    $status = ($status) ? '<span class="text-danger">'.$LANG['disable'].'</span>' : '<span class="text-success">'.$LANG['enable'].'</span>';
            
       echo '<tr>
                <td style="padding-top: 13px;">'.$title.'</td>
                <td>'.$status.'</td>
                <td style="padding-top: 13px;">'.$orders.'</td>
                <td>
                  <a href="'.$modulelink.'&a=manage&id='.$id.'" class="btn btn-success btn-sm"><i class="fa fa-pencil-square-o"></i> '.$LANG['edit'].'</a>
                  <a href="'.$modulelink.'&a=delete&id='.$id.'" class="btn btn-danger btn-sm" onclick="return confirm(\''.$LANG['deletehelp'].'\');"><i class="fa fa-trash-o"></i> '.$LANG['delete'].'</a>
                </td>
              </tr>';
  }            
            
  if(!$id)
  {
    echo '<tr>
            <td colspan="4" class="text-center">'.$LANG['norecordsfound'].'</td>
          </tr>';
  }
  
      echo '</tbody>
          </table>
        </div>';
}

if($_REQUEST['a'] == 'manage')
{
  $id = $_REQUEST['id'];

  if($id)
  {
    $multilang_title    = array();
    $multilang_message = array();

    $data = Capsule::table('mod_privacypolicy')->where('language', '')->where('id', $id)->first();

    $title    = $data->title;
    $message  = $data->contents;
    $disable  = $data->status;
    $sorts    = $data->orders;
  
    $result = Capsule::table('mod_privacypolicy')->where('parentid', $id)->get();
    foreach ($result as $data2)
    {
      $language                      = $data2->language;
      $multilang_title[$language]    = $data2->title;
      $multilang_message[$language] = $data2->contents;
    }  
  }
  
  if($id){echo '<h1><i class="fa fa-pencil-square-o"></i> '.$LANG['editprivacypolicy'].'</h1>';}else{echo '<h1><i class="fa fa-pencil-square-o"></i> '.$LANG['createnewprivacypolicy'].'</h1>';}

  echo '<script type="text/javascript">
  function showtranslation(language) {
    var translationElement = $("#translation_" + language),
        copiedSettings = tinymceSettings,
        selector = "textarea#" + language;
    translationElement.slideToggle(400, function() {
        if (translationElement.is(":visible")) {
            //We are displaying this row
            copiedSettings.selector = selector;
            tinymce.init(copiedSettings);
        } else {
            tinymce.remove(selector);
        }
    });
}
  </script>';
  echo '<script type="text/javascript" src="/assets/js/tinymce/tinymce.min.js"></script>';

  echo '<form name="form" action="'.$modulelink.'&a=save&id='.$id.'" method="POST" class="form-horizontal">
          <div class="panel panel-default">
            <div class="panel-heading">
              <a href="'.$modulelink.'" class="btn btn-danger btn-sm"><i class="fa fa-chevron-left"></i> '.$LANG['back'].'</a>
              <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-check"></i> '.$LANG['savechanges'].'</button>
              <button type="reset" class="btn btn-default btn-sm"><i class="fa fa-times"></i> '.$LANG['cancel'].'</button>
            </div>
            <div class="panel-body">
            
              <div class="form-group">
                <label class="col-md-3 control-label">'.$LANG['title'].'</label>
                <div class="col-md-6">
                  <input type="text" name="title" value="'.$title.'" class="form-control"> 
                </div>
              </div>
              <div class="form-group">
                <label for="answer" class="col-md-3 control-label">'.$LANG['contains'].'</label>
                <div class="col-md-9">
                  <textarea class="tinymce" name="message">'.$message.'</textarea>
                  <span class="help-block">'.$LANG['containshelp'].'</span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">'.$LANG['sortorder'].'</label>
                <div class="col-md-3">
                  <input type="text" name="sorts" value="'.$sorts.'" class="form-control"> 
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">'.$LANG['disable'].'</label>
                <div class="col-md-6">
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" name="disable" '; if ($disable){ echo'CHECKED'; } echo '>'.$LANG['disablehelp'].'
                    </label>
                  </div>
                </div>
              </div>';
              
   echo '<div class="page-header">
            <h4>'.$LANG['multilingualtranslations'].'</h4>
          </div>';

  foreach(Lang::getLanguages() as $language)
  {
    if($language != $CONFIG['Language'])
    {
  echo '<div class="panel panel-default"><a href="#" style="text-decoration:none" onClick="showtranslation(\''.$language.'\');return false;"><div class="panel-footer"><b><i class="fa fa-plus"></i> '.ucfirst($language).'</b></div></a></div>';
  echo '<div id="translation_'.$language.'" '; if(!$multilang_title[$language]){ echo 'style="display:none;"'; } echo '>';
  
    echo '<div class="form-group">
            <label class="col-md-3 control-label">'.$LANG['title'].'</label>
            <div class="col-md-6">
              <input type="text" name="multilang_title['.$language.']" value="'.$multilang_title[$language].'" class="form-control"> 
            </div>
          </div>
          <div class="form-group">
            <label for="answer" class="col-md-3 control-label">'.$LANG['contains'].'</label>
            <div class="col-md-9">
              <textarea id="'.$language.'" '; if($multilang_title[$language]){ echo 'class="tinymce"'; }else{echo 'class="tinymce-additional"';} echo ' name="multilang_message['.$language.']">'.$multilang_message[$language].'</textarea>
              <span class="help-block">'.$LANG['containshelp'].'</span>
            </div>
          </div>';              
  
  echo '</div>';

    }
  }

     echo '</div>
            <div class="panel-footer">
              <a href="'.$modulelink.'" class="btn btn-danger btn-sm"><i class="fa fa-chevron-left"></i> '.$LANG['back'].'</a>
              <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-check"></i> '.$LANG['savechanges'].'</button>
              <button type="reset" class="btn btn-default btn-sm"><i class="fa fa-times"></i> '.$LANG['cancel'].'</button>
            </div>
          </div>
        </form>';          
echo '<script type="text/javascript">
    var tinymceSettings = {
        selector: "textarea.tinymce",
        height: 300,
        theme: "modern",
        entity_encoding: "raw",
        plugins: "autosave print preview searchreplace autolink directionality visualblocks visualchars fullscreen image link media template code codesample table charmap hr pagebreak nonbreaking anchor insertdatetime advlist lists textcolor wordcount contextmenu colorpicker textpattern help imagetools",
        toolbar: [
            "formatselect,fontselect,fontsizeselect,|,bold,italic,strikethrough,underline,forecolor,backcolor,|,link,unlink,|,justifyleft,justifycenter,justifyright,justifyfull,|,search,replace,|,bullist,numlist,",
            "outdent,indent,blockquote,|,undo,redo,|,cut,copy,paste,pastetext,pasteword,|,table,|,hr,|,sub,sup,|,charmap,image,media,|,print,|,ltr,rtl,|,fullscreen,|,help,code,removeformat"
        ],
        image_advtab: true,
        content_css: [
            "/assets/fonts/css/lato.css",
            "//www.tinymce.com/css/codepen.min.css"
        ],
        browser_spellcheck: true,
        convert_urls : false,
        relative_urls : false,
        forced_root_block : "p",
        media_poster: false,
        mobile: {
            theme: "mobile",
            plugins: ["autosave", "lists", "autolink"],
            toolbar: ["undo", "bold", "italic", "styleselect"]
        },
        setup: "cleanSmartyTags",
        menu: {
            file: {title: "File", items: "preview | print"},
            edit: {title: "Edit", items: "undo redo | cut copy paste pastetext | selectall | searchreplace"},
            view: {title: "View", items: "visualaid visualchars visualblocks | preview fullscreen"},
            insert: {title: "Insert", items: "image anchor link media codesample | charmap hr"},
            format: {title: "Format", items: "bold italic strikethrough underline superscript subscript codeformat | blockformats align | removeformat"},
            table: {title: "Table", items: "inserttable tableprops deletetable | cell row column"},
            help: {title: "Help", items: "help | code"}
        }
    };
    function decodeHTMLEntities(text) {
        var entities = [
            ["amp", "&"],
            ["apos", "\'"],
            ["#x27", "\'"],
            ["#x2F", "/"],
            ["#39", "\'"],
            ["#47", "/"],
            ["lt", "<"],
            ["gt", ">"],
            ["nbsp", " "],
            ["quot", "\""]
        ];
        for (var i = 0, max = entities.length; i < max; ++i) {
            text = text.replace(new RegExp("&" + entities[i][0] + ";", "g"), entities[i][1]);
        }
        return text;
    }
    function cleanSmartyTags(inst) {
        inst.on("BeforeSetContent", function(e) {
            e.content = e.content.replace(/{foreach[^}]*}/g, function(str) {
                return decodeHTMLEntities(str);
            });
        });
        inst.on("PostProcess", function(e) {
            e.content = e.content.replace(/{foreach[^}]*}/g, function(str) {
                return decodeHTMLEntities(str);
            })
        });
    };
    $(document).ready(function() {
        tinymce.init(tinymceSettings).then(function(editors){
            editorLoaded = true;
            tinymce.addI18n("image", {
    "Image list": "Recent images"
});
        });
    });

    var editorEnabled = true,
        editorLoaded = false;

    function toggleEditor() {
        if (editorEnabled === true) {
            tinymce.activeEditor.remove();
            editorEnabled = false;
        } else {
            tinymce.init(tinymceSettings);
            editorEnabled = true;
        }
    }

    function insertMergeField(mfield) {
        tinymce.activeEditor.insertContent("{$" + mfield + "}");
    }
</script>';

}

if($_REQUEST['a'] == 'save')
{
  $id                 = $_REQUEST['id'];
  $title              = $_REQUEST['title'];
  $message            = $_REQUEST['message'];
  $sorts              = $_REQUEST['sorts'];
  $disable            = $_REQUEST['disable'];
  $multilang_title    = $_REQUEST['multilang_title'];
  $multilang_message  = $_REQUEST['multilang_message'];

  if($id)
  {
    Capsule::table('mod_privacypolicy')->where('id', $id)->update(array(
                                                                'title'    => $title, 
                                                                'contents' => $message, 
                                                                'status'   => $disable, 
                                                                'orders'   => $sorts, 
                                                                ));
  }
  else
  {
    $id = Capsule::table('mod_privacypolicy')->insertGetId(array(
                                                                'title'    => $title, 
                                                                'contents' => $message, 
                                                                'status'   => $disable, 
                                                                'orders'   => $sorts, 
                                                                ));
  }
  
  foreach ($multilang_title as $language => $title)
  {
    Capsule::table('mod_privacypolicy')->where('parentid', $id)->where('language', $language)->delete();
  
    if($title)
    {
      Capsule::table('mod_privacypolicy')->insert(array(
                                                              'parentid' => $id, 
                                                              'title'    => $multilang_title[$language], 
                                                              'contents' => $multilang_message[$language], 
                                                              'orders'   => $sorts, 
                                                              'status'   => $disable, 
                                                              'language' => $language, 
                                                              ));
    }
  }
  
  header('Location: '.$modulelink.'&success=true');
  exit; 
}

if($_REQUEST['a'] == 'settings')
{
  Capsule::table('mod_privacyconfog')->where('setting', 'Description')->update(array('value' => $_REQUEST['description']));
  Capsule::table('mod_privacyconfog')->where('setting', 'Keyword')->update(array('value' => $_REQUEST['keywords']));
  
  header('Location: '.$modulelink.'&success=true');
  exit;
}

if($_REQUEST['a'] == 'delete')
{
  $id = $_REQUEST['id'];

  Capsule::table('mod_privacypolicy')->where('id', $id)->delete();
  Capsule::table('mod_privacypolicy')->where('parentid', $id)->delete();
  
  header('Location: '.$modulelink);
  exit;
}
