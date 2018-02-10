#################
Crear nuevos elementos
#################

Ahora que sabes como leer datos de una base de datos usando CodeIgniter, 
pero aún no haz escrito ninguna información en la base de datos. En esta sección
expandirá su nuevo controlador y modelo creado anteriormente para incluir
esta funcionalidad.

Crear una forma
-------------

Para ingresar datos en la base de datos necesitas crear un formulario donde puedas
ingresar la información que se almacenará. Esto significa que necesitarás una forma
con dos campos, uno para el título y uno para el texto. Derivarás
la cadena de nuestro título en el modelo. Creando la nueva vista en el modelo
*application/views/news/create.php*.

::

    <h2><?php echo $title; ?></h2>

    <?php echo validation_errors(); ?>

    <?php echo form_open('news/create'); ?>

        <label for="title">Title</label> 
        <input type="input" name="title" /><br />

        <label for="text">Text</label>
        <textarea name="text"></textarea><br />

        <input type="submit" name="submit" value="Create news item" /> 

    </form>

solamente hay dos cosas aquí que probablemente no te resultarán familiares: la
función ``form_open()`` y la función ``validation_errors()``.

La primera función la proporciona el :doc:`form
helper <../helpers/form_helper>` y da la forma al elemento y 
funcionalidades extra, como añadir un oculto :doc:`CSRF prevention
field <../libraries/security>`. Este último se usa para informar
errores relacionados con la validación de formulario.

Yendo de regreso a tu controlador de noticias. Harás dos cosas aquí,
checar si la forma fue presentada y si los datos presentados
pasaron las reglas de validación. usarás el :doc:`form
validation <../libraries/form_validation>` para hacer esto.

::

    public function create()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');
        
        $data['title'] = 'Create a news item';
        
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('text', 'Text', 'required');
        
        if ($this->form_validation->run() === FALSE)
        {
            $this->load->view('templates/header', $data);   
            $this->load->view('news/create');
            $this->load->view('templates/footer');
            
        }
        else
        {
            $this->news_model->set_news();
            $this->load->view('news/success');
        }
    }

El código anterior agrega muchas funcionalidades. Las primeras líneas cargan la
ayuda de formas y el archivo de validación de formas. después de eso, las reglas para la
validación de formas son puestas. El método ``set_rules()`` toma tres argumentos;
el nombre del campo de entrada, el nombre que se usará en los mensajes de error, y
la regla. En este caso el título y campos de texto son requeridos.

CodeIgniter tiene una potente biblioteca de validación de formularios como se demostró
antes. Puedes leer :doc:`more about this library
here <../libraries/form_validation>`.

Continuando hacia abajo, puede ver una condición que verifica si la validación
del formulario ocurrió exitosamente. Si no fue así, el formulario se muestra, si
fue enviado **y** paso todas las reglas, el modelo es llamado. Después
de esto, una página se carga para mostrar un mensaje de éxito. Crea una página en
*application/views/news/success.php* y escribe un mensaje de éxito.

Modelo
-----

Lo único que queda es escribir un método que escriba los datos en
la base de datos. Usarás el Query Builder class para insertar la
información y utilizar la biblioteca de entrada para obtener los datos publicados. Abre
el modelo creado antesy añade lo siguiente:

::

    public function set_news()
    {
        $this->load->helper('url');
        
        $slug = url_title($this->input->post('title'), 'dash', TRUE);
        
        $data = array(
            'title' => $this->input->post('title'),
            'slug' => $slug,
            'text' => $this->input->post('text')
        );
        
        return $this->db->insert('news', $data);
    }

Este nuevo método se encarga de insertar la noticia en la base de datos.
la tercera línea contiene una nueva función, url\_title(). Esta función -
proveída por el :doc:`URL helper <../helpers/url_helper>` - elimina 
lo que acabas de pasar, reemplazando todos los espacios por guiones (-) y hac
seguro todo que esté en minúsculas. Esto te deja con una bonita
cadena, perfecta para crear URIs.

Continuemos con la preparación del registro que se va a insertar
después, dentro del apartado ``$data``. Cada elemento corresponde a una columna
de la tabla de base de datos creada anteriormente. Deberías percatarte de un nuevo método aquí,
llamado el método ``post()`` del :doc:`input
library <../libraries/input>`. Este método se encarga de que los datos estén
sanos, protegiéndote de ataques de otros. La biblioteca
de entrada se carga por defecto. Al último, insertarás nuestro apartado ``$data`` en
nuestra base de datos.

Enrutamiento
-------

Antes de que empieces a integrar nuevos elementos en tu aplicación CodeIgniter 
tienes que añadir una relga extra al *config/routes.php* file. Asegurandote de que tu
archivo contiene lo siguiente. Esto hace seguro que CodeIgniter vea 'create'
como un método en lugar de la cadena de la noticia.

::

    $route['news/create'] = 'news/create';
    $route['news/(:any)'] = 'news/view/$1';
    $route['news'] = 'news';
    $route['(:any)'] = 'pages/view/$1';
    $route['default_controller'] = 'pages/view';

Apunte su navegador a su entorno de desarrollo local donde ha
instalado CodeIgniter y añade index.php/news/create a la URL.
Felicidades, haz creado tu primera aplicación de CodeIgniter!
Agregue algunas noticias y revise las diferentes páginas que ha creado.
