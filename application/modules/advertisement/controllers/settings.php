<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * settings controller
 */
class settings extends Admin_Controller {
    //--------------------------------------------------------------------

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();

        $this->auth->restrict('Shippingrates.Settings.View');
        $this->lang->load('advertisement');

        $this->load->model('slide_model', null, true);
        $this->load->model('book_country_model', null, true);

        $this->load->model('localization/country_model', null, true);

        Template::set_block('sub_nav', 'settings/_sub_nav');

        Assets::add_module_js('shippingrates', 'shippingrates.js');
    }

    //--------------------------------------------------------------------

    /**
     * Displays a list of form data.
     *
     * @return void
     */
    public function index($country = '') {
        $this->load->helper('array');

        $countries = $this->country_model->find_all();
        if (empty($country) || !search_std_object($countries, $country))
            $country = $countries[0]->iso;

        if (isset($_POST['slide_title'])) {
            if ($this->validateSlides($errorSlide)) {
                $this->saveSlides();
            }
            else{
                Template::set('errorSlide', $errorSlide);
            }
        }

        if (isset($_POST['left_title'])) {
            if ($this->validateBook($errorLeftRight)) {
                $this->saveBook();
            }
            else{
                Template::set('errorLeftRight', $errorLeftRight);
            }
        }
        


        $slides = $this->slide_model->find_all_by("country_iso", $country);
        $slideTitles = array();
        $slideContents = array();
        $slideImages = array();
        if (is_array($slides) && count($slides) > 0) {
            foreach ($slides as $slide) {
                $slideTitles[] = $slide->title;
                $slideContents[] = $slide->content;
                $slideImages[] = $slide->image;
            }
        } else {
            for ($i = 0; $i < 3; $i++) {
                $slideTitles[] = '';
                $slideContents[] = '';
                $slideImages[] = '';
            }
        }

        Template::set('slideTitles', $slideTitles);
        Template::set('slideContents', $slideContents);
        Template::set('slideImages', $slideImages);
        
        $books = $this->book_country_model->find_all_by("country_iso", $country);
        if (is_array($books) && count($books) > 0) {
            foreach ($books as $book) {
                if (strtolower(trim($book->left_right)) == 'left') {
                    $leftTitle = $book->title;
                    $leftImageCurrent = $book->image;
                    $leftAuthor = $book->author;
                } else if (strtolower(trim($book->left_right)) == 'right') {
                    $rightTitle = $book->title;
                    $rightImageCurrent = $book->image;
                    $rightAuthor = $book->author;
                }
            }
        } else {
            $leftTitle = $leftImageCurrent = $rightTitle = $rightImageCurrent = '';
        }
        Template::set('leftTitle', $leftTitle);
        Template::set('leftImageCurrent', $leftImageCurrent);
        Template::set('leftAuthor', $leftAuthor);
        Template::set('rightTitle', $rightTitle);
        Template::set('rightImageCurrent', $rightImageCurrent);
        Template::set('rightAuthor', $rightAuthor);

        Template::set('current_country', $country);
        Template::set('countries', $countries);

        Template::set('toolbar_title', 'Manage advertisement');
        Template::set_view('settings/index');
        Template::render();
    }
    
    public function save() {
        $this->load->helper('array');

        
        if (isset($_POST['slide_title'])) {
            if ($this->validateSlides($errorSlide)) {
                $this->saveSlides();
            }
            else{
                Template::set('errorSlide', $errorSlide);
            }
        }

        if (isset($_POST['left_title'])) {
            if ($this->validateBook($errorLeftRight)) {
                $this->saveBook();
            }
            else{
                Template::set('errorLeftRight', $errorLeftRight);
            }
        }
        
        $this->load->library('user_agent');
        $preUrl = $this->agent->referrer();
        redirect($preUrl);

    }

    public function country($country) {
        $this->index($country);
    }

    private function saveSlides() {
        $slideTitles = $this->input->post('slide_title');
        $slideContents = $this->input->post('slide_content');
        $slideImageCurrents = $this->input->post('slide_image_current');
        $images = array();
        if (is_array($slideTitles) && count($slideTitles)) {
            for ($i = 0; $i < count($slideTitles); $i++) {
                if ($_FILES['slide_image']['name'][$i] != '') {
                    if (is_file('uploads/country/image/slide/' . $slideImageCurrents[$i])) {
                        @unlink('uploads/country/image/slide/' . $slideImageCurrents[$i]);
                    }
                    $image = $_FILES['slide_image']['name'][$i];
                    $extension = explode(".", $image);
                    $extension = $extension[count($extension) - 1];
                    $image = sprintf('_%s.' . $extension, uniqid(md5(time()), true));
                    $images[] = $image;
                } else {
                    $images[] = $slideImageCurrents[$i];
                }
            }

            $this->slide_model->delete_where("country_iso = '" . $_POST['iso'] . "'");

            for ($i = 0; $i < count($slideTitles); $i++) {
                $this->slide_model->insert(array(
                    'title' => htmlentities(trim($slideTitles[$i])),
                    'content' => htmlentities(trim($slideContents[$i])),
                    'image' => $images[$i],
                    'country_iso' => $_POST['iso']
                        )
                );
                move_uploaded_file($_FILES['slide_image']['tmp_name'][$i], 'uploads/country/image/slide/' . $images[$i]);
            }
        }
    }
    
    private function resize_image($file, $w, $h, $crop=FALSE) {
        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width-($width*abs($r-$w/$h)));
            } else {
                $height = ceil($height-($height*abs($r-$w/$h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w/$h > $r) {
                $newwidth = $h*$r;
                $newheight = $h;
            } else {
                $newheight = $w/$r;
                $newwidth = $w;
            }
        }
        $src = imagecreatefromjpeg($file);
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        return $dst;
    }


    private function saveBook() {
        $leftTitle = $this->input->post('left_title');
        $leftAuthor = $this->input->post('left_author');
        $leftImageCurrent = $this->input->post('left_image_current');
        
        $rightTitle = $this->input->post('right_title');
        $rightAuthor = $this->input->post('right_author');
        $rightImageCurrent = $this->input->post('right_image_current');
        
        $this->book_country_model->delete_where("country_iso = '" . $_POST['iso'] . "'");
        
        if ($_FILES['left_image']['name'] != '') {
            if (is_file('uploads/country/image/' . $leftImageCurrent)) {
                @unlink('uploads/country/image/' . $leftImageCurrent);
            }
            $imageLeft = $_FILES['left_image']['name'];
            $extension = explode(".", $imageLeft);
            $extension = $extension[count($extension) - 1];
            $imageLeft = sprintf('_%s.' . $extension, uniqid(md5(time()), true));
            move_uploaded_file($_FILES['left_image']['tmp_name'], 'uploads/country/image/' . $imageLeft);
        } else {
            $imageLeft = $leftImageCurrent;
        }
        
        if ($_FILES['right_image']['name'] != '') {
            if (is_file('uploads/country/image/' . $rightImageCurrent)) {
                @unlink('uploads/country/image/' . $rightImageCurrent);
            }
            $imageRight = $_FILES['right_image']['name'];
            $extension = explode(".", $imageRight);
            $extension = $extension[count($extension) - 1];
            $imageRight = sprintf('_%s.' . $extension, uniqid(md5(time()), true));
            move_uploaded_file($_FILES['right_image']['tmp_name'], 'uploads/country/image/' . $imageRight);
        } else {
            $imageRight = $rightImageCurrent;
        }
        
        $this->book_country_model->insert(array(
                                                'title' => htmlentities(trim($leftTitle)),
                                                'image' => $imageLeft,
                                                'left_right'=>'left',
                                                'author'=>$leftAuthor,
                                                'country_iso' => $_POST['iso']
                                            )
                );
        move_uploaded_file($_FILES['left_image']['tmp_name'][$i], 'uploads/country/image/' . $imageLeft);
        
        $this->book_country_model->insert(array(
                                                'title' => htmlentities(trim($rightTitle)),
                                                'image' => $imageRight,
                                                'left_right'=>'right',
                                                'author'=>$rightAuthor,
                                                'country_iso' => $_POST['iso']
                                            )
                );
        move_uploaded_file($_FILES['left_image']['tmp_name'][$i], 'uploads/country/image/' . $imageRight);
    }

    private function validateSlides(&$errorSlide) {
        $errorSlide="";
        $slideTitles = $this->input->post('slide_title');
        $slideContents = $this->input->post('slide_content');
        $slideImageCurrents = $this->input->post('slide_image_current');

        for ($i = 0; $i < count($slideTitles); $i++) {
            if (
                trim($slideTitles[$i]) == '' 
                || trim($slideContents[$i]) == '' 
                || (trim($slideImageCurrents[$i]) == '' && $_FILES['slide_image']['name'][$i]=='')
            ) {
                $errorSlide="Please input full information.";
                return FALSE;
            }
        }
        
        for ($i = 0; $i < count($slideTitles); $i++) {
            if ($_FILES['slide_image']['name'][$i]!=""&&strpos($_FILES['slide_image']['type'][$i], 'image') === FALSE) {
                $errorSlide = "Please upload the image file";
                return FALSE;
            }
        }
        
        return true;
    }
    
    private function validateBook(&$errorLeftRight) {
        $errorLeftRight="";
        $leftTitle = $this->input->post('left_title');
        $leftAuthor = $this->input->post('left_author');
        $leftImageCurrent = $this->input->post('left_image_current');
        
        $rightTitle = $this->input->post('right_title');
        $rightAuthor = $this->input->post('right_author');
        $rightImageCurrent = $this->input->post('right_image_current');

        if(
                trim($leftTitle)==""
                || trim($rightTitle)==""
                || trim($leftAuthor)==""
                || trim($rightAuthor)==""
                || (trim($leftImageCurrent) == '' && $_FILES['left_image']['name']=='')
                || (trim($rightImageCurrent) == '' && $_FILES['right_image']['name']=='')
                )
        {
            $errorLeftRight="Please input full information.";
            return FALSE;
        }
        
        if (($_FILES['left_image']['name']!=""&&strpos($_FILES['left_image']['type'], 'image') === FALSE) || ($_FILES['right_image']['name']!=""&&strpos($_FILES['right_image']['type'], 'image') === FALSE)) {
            $errorLeftRight = "Please upload the image file";
            return FALSE;
        }

        return true;
    }

    //--------------------------------------------------------------------
}
