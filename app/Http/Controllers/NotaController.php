<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\NotaFormRequest;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Curso;
use App\Grado;
use App\Asignatura;
use App\Nota;
use App\Matricula;
use App\Alumno;
use App\Fecha_Ingreso;

class NotaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:docente');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function cargarDatosAlumno($idgrado, $idasignatura, $idalumno)
    {
        $id = Auth::user()->id;
        $alumno = Alumno::where('id',$idalumno)->first(); //solo debe recuperar un alumno
        $matricula = Matricula::where([['id_alumno',$idalumno],['id_grado',$idgrado]])->first(); //igual
        $curso = Curso::where([['id_grado',$idgrado],['id_docente',$id],['id_asignatura',$idasignatura]])->first(); //igual
        //
        $grado = Grado::where('id',$idgrado)->first();
        $asignatura = Asignatura::where('id',$curso->id_asignatura)->first();
        //
        $nota1 = Nota::where([['id_matricula',$matricula->id],['id_curso',$curso->id],['trimestre','1']])->select('nota')->first();
        $nota2 = Nota::where([['id_matricula',$matricula->id],['id_curso',$curso->id],['trimestre','2']])->select('nota')->first();
        $nota3 = Nota::where([['id_matricula',$matricula->id],['id_curso',$curso->id],['trimestre','3']])->select('nota')->first();

        $n1 = ($nota1!=null)?$nota1->nota : 0;
        $n2 = ($nota2!=null)?$nota2->nota : 0;
        $n3 = ($nota3!=null)?$nota3->nota : 0;
        return view('notasdocente.create',compact('alumno','matricula','curso','grado','asignatura','n1','n2','n3'));
    }

    public function registrarNota(NotaFormRequest $request, $idmatricula, $idcurso, $id_alumno)
    {
        date_default_timezone_set('America/Lima');
        $fechaactual = date('Y-m-d');
        $curso = Curso::where('id',$idcurso)->first();
        $grado = Grado::where('id',$curso->id_grado)->first();
        $trimestre = $request->get('trimestre');
        $fecha_ingreso = Fecha_Ingreso::where([['anio_academico',$grado->anio_academico],
                                               ['trimestre',$trimestre]])->first();

        if($fecha_ingreso != null)
        {
            $fechainicio = $fecha_ingreso->fecha_inicio;
            $fechafin = $fecha_ingreso->fecha_fin;
            if(strtotime($fechainicio)<=strtotime($fechaactual) && strtotime($fechaactual)<=strtotime($fechafin))
            {
                $registro = Nota::where([['id_matricula',$idmatricula], ['id_curso',$idcurso],['trimestre',$trimestre]])->first();
                if($registro == null)
                {
                    $nota = new Nota;
                    $nota->id = $idmatricula.$idcurso.$trimestre;
                    $nota->id_matricula = $idmatricula;
                    $nota->id_curso = $idcurso;
                    $nota->nota = $request->get('nota');
                    $nota->trimestre = $trimestre;
                    $nota->observacion = $request->get('observaciones');
                    $nota->fecha_ing = $fechaactual; //date('Ymd'); //H:i:s
                    $nota->save();
                    return redirect("menudocentes/periodo/cursos/$curso->id_grado/$curso->id_asignatura/$id_alumno")->with('mensaje','Se inserto correctamente!!');
                }
                else
                {
                    $registro->nota=$request->get('nota');
                    $registro->observacion=$request->get('observaciones');
                    $registro->save();
                    return redirect("menudocentes/periodo/cursos/$curso->id_grado/$curso->id_asignatura/$id_alumno")->with('mensaje','Se actualizo correctamente!!');
                }
            }
            else {
                return redirect("menudocentes/periodo/cursos/$curso->id_grado/$curso->id_asignatura/$id_alumno")->with('mensaje','No es posible registrar la nota!!');
            }
        }
        else {
            return redirect("menudocentes/periodo/cursos/$curso->id_grado/$curso->id_asignatura/$id_alumno")->with('mensaje','No es posible registrar la nota!!');
        }
        #return view('docentesmenu.verAlumnosxCurso',compact('alumnos','grado','id_asignatura'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
