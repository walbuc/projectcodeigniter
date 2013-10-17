<?php if (!defined('BASEPATH')) die();
class Books extends CI_Controller {
   
   public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
	   $this->load->library('cart');
      $this->load->model('books_model');
		$this->load->helper('html');
      $this->load->library('tank_auth');
      $this->load->helper('form');
      $this->load->library('form_validation');
      
      
	}
   public function insert()
   {  
      $isbn = $_GET['id'];
      if($this->cart->contents()){
         $flag = false;
         foreach ($this->cart->contents() as $items):
	    if($items['id'] === $isbn){
	       $p = $items['qty'];
	       $p=$p+1;
		  
	       $data = array(
		    'rowid' => $items['rowid'],
		    'qty'   => $p,
		 );
	       $this->cart->update($data); 
	       $flag =true;   
	    }
         endforeach;

         if($flag == false){
            $p=1;
            $price = $this->books_model->get_price($isbn);
            $book = $this->books_model->get_books($isbn);
	    $stock = $this->books_model->get_stock($isbn);
            $author = $book['author'];
            if($stock == '0')
	    {
	       $p = 0;  
	    }
	    
	    $data = array(
                     'id'      => $isbn,
                     'qty'     => $p,
                     'price'   => $price,
                     'name'    => $author,
		     'stock'   => $stock
                  );
            $this->cart->insert($data);
         }

      }else{$p=1;
            $price = $this->books_model->get_price($isbn);
            $book = $this->books_model->get_books($isbn);
            $stock = $this->books_model->get_stock($isbn);
	    if($stock == 0)
	    {
	       $p = 0;  
	    }
	    
	    $author = $book['author'];
            $data = array(
                     'id'      => $isbn,
                     'qty'     => $p,
                     'price'   => $price,
                     'name'    => $author,
		     'stock'   => $stock
                  );
            $this->cart->insert($data);
      }

   if($p == 0)
   {
       echo  "<div>Sin stock!!!</div>";  
   }
   else
   {
      echo  "<div>Agregaste ".$p."!!</div>";
   }
      
   }
   
   public function index()
   {
      if ($this->session->flashdata('message')) {
      
	 $data['message'] = $this->session->flashdata('message');
      }else{
      $data['message'] = '';
      }

      if ($this->tank_auth->is_logged_in()) {
        $amount = $this->books_model->quantity_buy($this->session->userdata('user_id'));
        if($amount > 0){
         $descuento = $this->books_model->get_descuento_bycantidad($amount);
	 if($descuento > 0){
	    $this->session->set_userdata('descuento', $descuento);   
	 }
	 else{
	    $this->session->set_userdata('descuento', null);
	 }
        }
      }

      $data['featured']= $this->books_model->get_lasts_books();

      $data['books'] = $this->books_model->get_books();
      $data['title'] = 'Stock de Libros';
      $data1 = array();
      $data2 = array();
   
      foreach ($data['books'] as $books_item):
      $isbn=$books_item['isbn'];
      $image= $this->books_model->get_image($isbn);
      $books_item['image']= $image['image'];
      
      array_push($data1, $books_item);   
      endforeach;

      foreach ($data['featured'] as $books_item):
      $isbn=$books_item['isbn'];
      $image= $this->books_model->get_image($isbn);
      $books_item['image']= $image['image'];
      
      array_push($data2, $books_item);   
      endforeach;
      
      $data['featured'] = $data2;
      $data['books']=$data1;     
     
      $this->load->view('templates/header', $data);
      $this->load->view('templates/menubar', $data);
      $this->load->view('books/index', $data);
      $this->load->view('templates/footer');

   }
   public function view($isbn)
   {
      $data['books_item'] = $this->books_model->get_books($isbn);
      $data['book_review'] = $this->books_model->get_review($isbn);
      $data['book_image'] = $this->books_model->get_image($isbn);
      if (empty($data['books_item']))
	{print("entra aca");
		show_404();
	}

	$data['title'] = $data['books_item']['title'];

	$this->load->view('templates/header', $data);
	$this->load->view('books/view', $data);
	$this->load->view('templates/footer');
      
      
   }


   public function create(){
      $this->load->helper('form');
      $this->load->library('form_validation');
      
      $this->form_validation->set_rules('isbn', 'Isbn', 'required');
      $this->form_validation->set_rules('titulo', 'Title', 'required');
      $this->form_validation->set_rules('text', 'Reseña', 'required');
      $this->form_validation->set_rules('categoria', 'Categoria', 'required');
      $this->form_validation->set_rules('autor', 'Autor', 'required');
      $this->form_validation->set_rules('precio', 'Precio', 'required|numeric');

      $config['upload_path'] = './assets/img/libros';
      $config['allowed_types'] = 'gif|jpg|png';
      $config['max_size']  = '100';
      $config['max_width']  = '1024';
      $config['max_height']  = '768';
      $this->load->library('upload', $config);
      
     
      if ($this->form_validation->run() === FALSE || !$this->upload->do_upload())
      {

         $error = array('error' => $this->upload->display_errors());
         $this->load->view('templates/header');   
         $this->load->view('templates/menubar');
         $this->load->view('admin/books_form');
         $this->load->view('templates/footer');
      
      }
      else
      {
         $this->books_model->create_book();
         
         redirect('');//nuevo libro
      }


   }

   public function list_books(){

      //validadr admin

      $data['books'] = $this->books_model->get_books();
      $data['title'] = 'Stock de Libros';
      $data1 = array();
   
      foreach ($data['books'] as $books_item):
      $isbn=$books_item['isbn'];
      $image= $this->books_model->get_image($isbn);
      $books_item['image']= $image['image'];
      
      array_push($data1, $books_item);   
      endforeach;
      $data['books']=$data1;     
     
      $this->load->view('templates/header', $data);
      $this->load->view('templates/menubar', $data);
      $this->load->view('admin/index', $data);
      $this->load->view('templates/footer');
   }

   public function update($isbn){
      $this->load->helper('form');
      $this->load->library('form_validation');
      $data['books_item'] = $this->books_model->get_books($isbn);
      $data['book_review'] = $this->books_model->get_review($isbn);
      $data['book_image'] = $this->books_model->get_image($isbn);
      
      $this->form_validation->set_rules('isbn', 'Isbn', 'required');
      $this->form_validation->set_rules('titulo', 'Title', 'required');
      $this->form_validation->set_rules('text', 'Reseña', 'required');
      $this->form_validation->set_rules('categoria', 'Categoria', 'required');
      $this->form_validation->set_rules('autor', 'Autor', 'required');
      $this->form_validation->set_rules('precio', 'Precio', 'required|numeric');

      $config['upload_path'] = './assets/img/libros';
      $config['allowed_types'] = 'gif|jpg|png';
      $config['max_size']  = '100';
      $config['max_width']  = '1024';
      $config['max_height']  = '768';
      $this->load->library('upload', $config);
      
     
      if (empty($data['books_item']))
      {
         show_404();
      }

      $data['title'] = $data['books_item']['title'];

      if ($this->form_validation->run() === FALSE)
      {
      
      $this->load->view('templates/header', $data);
      $this->load->view('admin/book_update', $data);
      $this->load->view('templates/footer');
      }else{

         $this->books_model->update_book();
         redirect(site_url().'books/admin/list');
      }
   }

   public function delete($isbn){
      $this->books_model->delete_book($isbn);
      redirect(site_url().'books/admin/list');
   }
   
   public function search(){
      $busqueda = $this->input->post('busqueda');
      $data['books'] = $this->books_model->search_books($busqueda);
      $data1 = array();
      
      foreach ($data['books'] as $books_item):
	 $isbn = $books_item['isbn'];
	 $image = $this->books_model->get_image($isbn);
	 $books_item['image'] = $image['image'];
	 array_push($data1, $books_item);   
      endforeach;
      
      $data['books']=$data1;
      if(count($data['books']) > 0){
	 $data['Mensaje'] = count($data['books'])." Resultados para la búsqueda: '".$busqueda."'";
      }
      else{
	 $data['Mensaje'] = "Ningun resultado para la búsqueda: '".$busqueda."'";
      }
     
      $this->load->view('templates/header');
      $this->load->view('templates/menubar');
      $this->load->view('books/search_results', $data);
      $this->load->view('templates/footer');
   }

}

/* Location: ./application/controllers/books.php */
