<?php 

namespace App;
use Source\Controller\Controller; 
use Source\Model\Postagem;
use Source\Model\User;


class PostController extends Controller
{
	private $postagem;
    private $foto;
	function __construct()
	{
		if (!User::userLogado()) {
            //$this->message->error("Efetue login para acessar ao sistema.");
            redirect("/");

         }
		parent::__construct("erros");
		$this->postagem=(new Postagem());
	}

	public function home()
	{
        $this->engine->render("dashibord/home",
            [
              "postagem"=>$this->postagem->find()->order("id DESC")->limit(10)->fetch(true),
              "total"=>0,
              "mensal"=>0,
              "usuario"=>0
            ]

        );
	}

    public function listar()
    {
        $this->engine->render("postagem/home",
            [
              "postagem"=>$this->postagem->find()->order("id DESC")->limit(100)->fetch(true),
            ]

        );
    }

    public function store($data)
    {
        if (!isset($data["id"])) {
            $dads= new \stdClass();
            $dads->id="";
            $dads->title="";
            $dads->subtitulo="";
            $dads->content="";
            $dads->image_url="";
            $dads->tipo="";
            $dads->status="";
        }else{
            $id=base64_decode($data["id"]);
            $dads=$this->postagem->find("id=:id","id={$id}")->fetch();
        }
        $this->engine->render("postagem/cadastro",
            [

            "dados"=>$dads
            ]
      );
    }

        public function filtrar($data)
    {
        $da=$this->postagem->find("title LIKE :t","t=%{$data["nome"]}%")->fetch(true);


        echo json_encode(["html"=>$this->renderHtml($da)]);
    }



	public function save($data)
	{
		$post=$this->postagem;
          if (empty($data["titulo"])) 
          {
          	 echo json_encode(["error"=>"Campo Titulo é Obrigatório"]);
          	 return;
          }

          if (empty($data["resumo"])) 
          {
          	 echo json_encode(["error"=>"Campo Resumo é Obrigatório"]);
          	 return;
          }

          if (empty($data["descricao"])) 
          {
          	 echo json_encode(["error"=>"Campo Descricao é Obrigatório"]);
          	 return;
          }

          // Verificar se o arquivo foi enviado
    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['error' => 'Nenhum arquivo foi enviado ou ocorreu um erro.']);
        return false;
    }

   if (!$this->uploadFile($_FILES['foto'])) {
       return;
   }


          if ($data["id"]) {
          	$post->id=$data["id"];
          }
          $post->title=$data["titulo"];
          $post->subtitulo=$data["resumo"];
          $post->content=$data["descricao"];
          $post->image_url=url()."/".$this->foto;
          $post->tipo=$data["tipo"];
          $post->status="Activo";

          if (!$post->save()) {
          	echo json_encode(["error"=>$post->message()->getText()]);
          	return;
          }

          echo json_encode(["success"=>"Postagem adicionada com Sucesso"]);
          	return;
	}


    private function uploadFile($file)
    {


    // Validação do tipo de arquivo permitido
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        echo json_encode(['error' => 'Tipo de arquivo não permitido. Apenas JPEG, PNG e GIF são aceitos.']);
        return false;
    }

    // Validação do tamanho do arquivo (máximo 2 MB)
    $maxSize = 2 * 1024 * 1024; // 2 MB em bytes
    if ($file['size'] > $maxSize) {
        echo json_encode(['error' => 'O arquivo excede o tamanho máximo permitido de 2 MB.']);
        return false;
    }

    // Diretório de upload
    $uploadDir = 'Public/assets/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION); // Extensão do arquivo
    $newFileName = date('Ymd_His') . '_' . uniqid() . '.' . $extension;

    // Caminho completo do arquivo salvo
    $filePath = $uploadDir . $newFileName;

    // Mover o arquivo
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        $this->foto=$filePath;
        return true;
    } else {
        echo json_encode(['error' => 'Erro ao salvar o arquivo.']);
        return false;
    }

} 

private function renderHtml($data)
    { 

          return $this->view->v("postagem/tbl",["postagem"=>$data]);
    }

}