<?php
declare(strict_types=1);
require_once __DIR__ . "\..\Framework.php";
require_once "class.Rotas.php";
require_once "class.Response.php";

$rotas = new Rotas();

$rotas->get("/GetMangas/{id}","MangasController@Get");
$rotas->get("/GetMangasByTitulo/{titulo}","MangasController@Get");
$rotas->get("/GetAllMangas","MangasController@GetTodos");
$rotas->get("/GetMangasGeneros","MangasController@GetMangasGeneros");
$rotas->get("/GetMangaGeneroAutorCapitulos/{titulo}","MangasController@GetMangaGeneroAutorCapitulos");
$rotas->post("/InsertMangas","MangasController@Post");
$rotas->post("/InsertMangaComAutor","MangasController@PostComAutor");
$rotas->post("/UploadImagemManga","MangasController@UploadImagem");
$rotas->delete("/DeleteMangas/{id}","MangasController@Delete");
$rotas->put("/UpdateMangas/{id}","MangasController@Put");
$rotas->put("/UpdateMangaComAutor/{id}","MangasController@PutComAutor"); 

$rotas->get("/GetAutores/{id}","AutoresController@Get");
$rotas->get("/GetAllAutores","AutoresController@GetTodos");
$rotas->post("/InsertAutores","AutoresController@Post");
$rotas->delete("/DeleteAutores/{id}","AutoresController@Delete");
$rotas->put("/UpdateAutores/{id}","AutoresController@Put"); 

$rotas->get("/GetAvaliacoes/{id}","AvaliacoesController@Get");
$rotas->get("/GetAllAvaliacoes","AvaliacoesController@GetTodos");
$rotas->post("/InsertAvaliacoes","AvaliacoesController@Post");
$rotas->delete("/DeleteAvaliacoes/{id}","AvaliacoesController@Delete");
$rotas->put("/UpdateAvaliacoes/{id}","AvaliacoesController@Put"); 

$rotas->get("/GetCapitulos/{id}","CapitulosController@Get");
$rotas->get("/GetAllCapitulos","CapitulosController@GetTodos");
$rotas->post("/InsertCapitulos","CapitulosController@Post");
$rotas->delete("/DeleteCapitulos/{id}","CapitulosController@Delete");
$rotas->put("/UpdateCapitulos/{id}","CapitulosController@Put"); 

$rotas->get("/GetComentariosManga/{id}","ComentariosMangaController@Get");
$rotas->get("/GetAllComentariosManga","ComentariosMangaController@GetTodos");
$rotas->post("/InsertComentariosManga","ComentariosMangaController@Post");
$rotas->delete("/DeleteComentariosManga/{id}","ComentariosMangaController@Delete");
$rotas->put("/UpdateComentariosManga/{id}","ComentariosMangaController@Put"); 

$rotas->get("/GetFavoritos/{id}","FavoritosController@Get");
$rotas->get("/GetAllFavoritos","FavoritosController@GetTodos");
$rotas->post("/InsertFavoritos","FavoritosController@Post");
$rotas->delete("/DeleteFavoritos/{id}","FavoritosController@Delete");
$rotas->put("/UpdateFavoritos/{id}","FavoritosController@Put"); 

$rotas->get("/GetGeneros/{id}","GenerosController@Get");
$rotas->get("/GetMangaPorId/{id}","GenerosController@GetMangaPorId");
$rotas->get("/GetGeneroPorNome/{nome}","GenerosController@Get");
$rotas->get("/GetAllGeneros","GenerosController@GetTodos");
$rotas->post("/InsertGeneros","GenerosController@Post");
$rotas->delete("/DeleteGeneros/{id}","GenerosController@Delete");
$rotas->put("/UpdateGeneros/{id}","GenerosController@Put"); 

$rotas->get("/GetGenerosMangas/{id}","GenerosMangasController@Get");
$rotas->get("/GetAllGenerosMangas","GenerosMangasController@GetTodos");
$rotas->post("/InsertGenerosMangas","GenerosMangasController@Post");
$rotas->delete("/DeleteGenerosMangas/{id}","GenerosMangasController@Delete");
$rotas->put("/UpdateGenerosMangas/{id}","GenerosMangasController@Put"); 

$rotas->get("/GetRankings/{id}","RankingsController@Get");
$rotas->get("/GetAllRankings","RankingsController@GetTodos");
$rotas->post("/InsertRankings","RankingsController@Post");
$rotas->delete("/DeleteRankings/{id}","RankingsController@Delete");
$rotas->put("/UpdateRankings/{id}","RankingsController@Put"); 

$rotas->get("/GetUsuarios/{id}","UsuariosController@Get");
$rotas->get("/GetAllUsuarios","UsuariosController@GetTodos");
$rotas->post("/InsertUsuarios","UsuariosController@Post");
$rotas->delete("/DeleteUsuarios/{id}","UsuariosController@Delete");
$rotas->put("/UpdateUsuarios/{id}","UsuariosController@Put"); 

echo json_encode($rotas->executar());