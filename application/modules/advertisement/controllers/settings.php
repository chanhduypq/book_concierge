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

        $slides = $this->slide_model->find_all_by("country_iso", $country);
        $slideTitles = array();
        $slideContents = array();
        $slideImages = array();
        $slideLinks = array();
        $slideIds = array();
        if (is_array($slides) && count($slides) > 0) {
            foreach ($slides as $slide) {
                $slideTitles[] = $slide->title;
                $slideContents[] = $slide->content;
                $slideImages[] = $slide->image;
                $slideLinks[] = $slide->read_more_link;
                $slideIds[] = $slide->id;
            }
        } else {
            for ($i = 0; $i < 3; $i++) {
                $slideTitles[] = '';
                $slideContents[] = '';
                $slideImages[] = '';
                $slideLinks[] = '';
                $slideIds[]='';
            }
        }

        Template::set('slideTitles', $slideTitles);
        Template::set('slideContents', $slideContents);
        Template::set('slideImages', $slideImages);
        Template::set('slideLinks', $slideLinks);
        Template::set('slideIds', $slideIds);
        
        $books = $this->book_country_model->find_all_by("country_iso", $country);
        if (is_array($books) && count($books) > 0) {
            foreach ($books as $book) {
                if (strtolower(trim($book->left_right)) == 'left') {
                    $leftTitle = $book->title;
                    $leftImageCurrent = $book->image;
                    $leftAuthor = $book->author;
                    $leftLink = $book->read_more_link;
                    $leftId=$book->id;
                } else if (strtolower(trim($book->left_right)) == 'right') {
                    $rightTitle = $book->title;
                    $rightImageCurrent = $book->image;
                    $rightAuthor = $book->author;
                    $rightLink = $book->read_more_link;
                    $rightId=$book->id;
                }
            }
        } else {
            $leftTitle = $leftImageCurrent = $rightTitle = $rightImageCurrent = $leftId = $rightId = $leftLink= $rightLink = '';
        }
        Template::set('leftTitle', $leftTitle);
        Template::set('leftImageCurrent', $leftImageCurrent);
        Template::set('leftAuthor', $leftAuthor);
        Template::set('leftLink', $leftLink);
        Template::set('rightTitle', $rightTitle);
        Template::set('rightImageCurrent', $rightImageCurrent);
        Template::set('rightAuthor', $rightAuthor);
        Template::set('rightLink', $rightLink);
        Template::set('leftId', $leftId);
        Template::set('rightId', $rightId);

        Template::set('current_country', $country);
        Template::set('countries', $countries);

        Template::set('toolbar_title', 'Manage advertisement');
        Template::set_view('settings/index');
        Template::render();
    }
    
    public function save() {
        $this->load->helper('array');
        
        if (isset($_POST['slide_title'])) {
            $this->saveSlides();
        }

        if (isset($_POST['left_title'])) {
            $this->saveBook();
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
        $slideLinks = $this->input->post('read_more_link');
        $images = array();
        if (is_array($slideTitles) && count($slideTitles)) {
            for ($i = 0; $i < count($slideTitles); $i++) {
                if ($_FILES['slide_image_'.$i]['name']!='') {
                    if (is_file('uploads/country/image/slide/' . $this->input->post('slide_image_current_'.$i))) {
                        @unlink('uploads/country/image/slide/' . $this->input->post('slide_image_current_'.$i));
                    }
                    $image = $_FILES['slide_image_'.$i]['name'];
                    $extension = explode(".", $image);
                    $extension = $extension[count($extension) - 1];
                    $image = sprintf('_%s.' . $extension, uniqid(md5(time()), true));
                    $images[] = $image;
                    move_uploaded_file($_FILES['slide_image_'.$i]['tmp_name'], 'uploads/country/image/slide/' . $image);
                } else {
                    $images[] = $this->input->post('slide_image_current_'.$i);
                }
            }

            $this->slide_model->delete_where("country_iso = '" . $_POST['iso'] . "'");

            for ($i = 0; $i < count($slideTitles); $i++) {
                if(trim($slideLinks[$i])==''){
                    $link=NULL;
                }
                else{
                    $link=htmlentities(trim($slideLinks[$i]));
                }
                $this->slide_model->insert(array(
                    'title' => htmlentities(trim($slideTitles[$i])),
                    'content' => htmlentities(trim($slideContents[$i])),
                    'read_more_link' => $link,
                    'image' => $images[$i],
                    'country_iso' => $_POST['iso']
                        )
                );
                
            }
        }
    }
    
    private function saveBook() {
        $leftTitle = $this->input->post('left_title');
        $leftAuthor = $this->input->post('left_author');
        $read_more_link_left = $this->input->post('read_more_link_left');
        $leftImageCurrent = $this->input->post('left_image_current');
        
        $rightTitle = $this->input->post('right_title');
        $rightAuthor = $this->input->post('right_author');
        $rightImageCurrent = $this->input->post('right_image_current');
        $read_more_link_right = $this->input->post('read_more_link_right');
        
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
                                                'read_more_link'=>$read_more_link_left,
                                                'country_iso' => $_POST['iso']
                                            )
                );
        
        $this->book_country_model->insert(array(
                                                'title' => htmlentities(trim($rightTitle)),
                                                'image' => $imageRight,
                                                'left_right'=>'right',
                                                'read_more_link'=>$read_more_link_right,
                                                'author'=>$rightAuthor,
                                                'country_iso' => $_POST['iso']
                                            )
                );
    }

}
