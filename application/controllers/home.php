<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Bonfire
 *
 * An open source project to allow developers get a jumpstart their development of CodeIgniter applications
 *
 * @package   Bonfire
 * @author    Bonfire Dev Team
 * @copyright Copyright (c) 2011 - 2013, Bonfire Dev Team
 * @license   http://guides.cibonfire.com/license.html
 * @link      http://cibonfire.com
 * @since     Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Home controller
 *
 * The base controller which displays the homepage of the Bonfire site.
 *
 * @package    Bonfire
 * @subpackage Controllers
 * @category   Controllers
 * @author     Bonfire Dev Team
 * @link       http://guides.cibonfire.com/helpers/file_helpers.html
 *
 */
class Home extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('application');
		$this->load->library('Template');
		$this->load->library('Assets');
		$this->lang->load('application');
		$this->load->library('events');
	}

	//--------------------------------------------------------------------

	/**
	 * Displays the homepage of the Bonfire app
	 *
	 * @return void
	 */
	public function index($category='default')
	{   
                        
            $available_categories = array('default'=>0, 'children'=>4, 'management'=>3, 'religion'=>22, 'crime'=>18);
            
            if (!array_key_exists($category, $available_categories))
                $category = 'default';
        
            $this->load->library('users/auth');
            $this->set_current_user();

            $this->load->helper('books/books');
            
            Template::set('category', $category);
            Template::set('category_id', $available_categories[$category]);
            
            $this->load->model('advertisement/slide_model', null, true);
            $this->load->model('advertisement/book_country_model', null, true);
            
            $this->load->library('session');
            
            if($this->session->userdata('country_iso')){
                $country=$this->session->userdata('country_iso');
            }
            else{
                $this->load->model('localization/country_model');
                $countries_data = $this->country_model->order_by('group')->order_by('name')->find_all();
                $country=$countries_data[0]->iso;
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
                $leftTitle = $leftImageCurrent = $leftAuthor = $rightTitle = $rightImageCurrent = $rightAuthor = '';
            }
            Template::set('leftTitle', $leftTitle);
            Template::set('leftImageCurrent', $leftImageCurrent);
            Template::set('leftAuthor', $leftAuthor);
            Template::set('rightTitle', $rightTitle);
            Template::set('rightImageCurrent', $rightImageCurrent);
            Template::set('rightAuthor', $rightAuthor);
            Template::set('isHomePage', $this->uri->segment(1) === FALSE);
            
            Template::render();
	}//end index()

        public function hot_this_week() {
		$this->index();
	}
        
        public function useragreement()
        {
            Template::render();
        }

	//--------------------------------------------------------------------

	/**
	 * If the Auth lib is loaded, it will set the current user, since users
	 * will never be needed if the Auth library is not loaded. By not requiring
	 * this to be executed and loaded for every command, we can speed up calls
	 * that don't need users at all, or rely on a different type of auth, like
	 * an API or cronjob.
	 *
	 * Copied from Base_Controller
	 */
	protected function set_current_user()
	{
		if (class_exists('Auth'))
		{
			// Load our current logged in user for convenience
			if ($this->auth->is_logged_in())
			{
				$this->current_user = clone $this->auth->user();

				$this->current_user->user_img = gravatar_link($this->current_user->email, 22, $this->current_user->email, "{$this->current_user->email} Profile");

				// if the user has a language setting then use it
				if (isset($this->current_user->language))
				{
					$this->config->set_item('language', $this->current_user->language);
				}
			}
			else
			{
				$this->current_user = null;
			}

			// Make the current user available in the views
			if (!class_exists('Template'))
			{
				$this->load->library('Template');
			}
			Template::set('current_user', $this->current_user);
		}
	}

	//--------------------------------------------------------------------
}//end class