<?php


namespace App\Tests\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class LoginTest extends WebTestCase
{

    public function testLoginIndex()
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', 'es/login');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        //REQ-PUB-LOG-01: Caja para introducir el correo
        $this->assertEquals(1, $crawler->filter('input[type=email]')->count(),'Se esperaba una 1 caja de texto para introducir el e-mail');

        //REQ-PUB-LOG-02: Caja para introducir la contraseña
        $this->assertEquals(1, $crawler->filter('input[type=password]')->count(),'Se esperaba una 1 caja de texto para introducir la contraseña');

        //REQ-PUB-LOG-03: Botón con el texto “Entrar”
        $botonsubmit = $crawler->filter('button[type=submit]');
        $this->assertEquals(1, $botonsubmit->count(),'Se esperaba un boton "Entrar"');
        $this->assertContains(
            'Entrar',
            $botonsubmit->text(), 'No hay ningún botón entrar'
        );

        //REQ-PUB-LOG-04: Al darle a Entrar, si la caja del correo está vacía, mostrará el mensaje “Debes indicar el correo”.
        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
        $form = $botonsubmit->form(['password'=>'adfdasf','_csrf_token'=>$csrfToken]);
        $responseform = $client->submit($form);
        $this->assertContains('Debes indicar el correo', $responseform->text(),'No se indica mensaje cuando el correo no se rellena');

        //REQ-PUB-LOG-05: Al darle a Entrar, si la caja del correo está vacía, mostrará el mensaje “Debes indicar la contraseña”.
        //$csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('_csrf_token');
        $form = $botonsubmit->form(['email'=>'adfdsaf','_csrf_token'=>$csrfToken]);
        $responseform = $client->submit($form);
        $this->assertContains('Debes indicar la contraseña', $responseform->text(),'No se indica mensaje cuando la contraseña no se rellena');

        //REQ-PUB-LOG-06: Al darle a Entrar, si los datos de identificación no son correctos mostrará el mensaje “Credenciales no válidas”.
        //$csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('_csrf_token');
        $form = $botonsubmit->form(['email'=>'aasdfd@adsfads.com','password'=>'adfdsaf','_csrf_token'=>$csrfToken]);
        $responseform = $client->submit($form);
        $this->assertContains('Credenciales no válidas', $responseform->text(),'No se indica mensaje cuando las credenciales son erroneas');

        //REQ-PUB-LOG-07: Al darle a Entrar, si los datos de identificación son correctos dirigirá al usuario a la página correspondiente según el rol del usuario".
        //
        //REQ-PUB-LOG-08: Si el usuario es un administrador, le dirigirá a la página del panel de control del administrador.
        $form = $botonsubmit->form(['email'=>'admin@app.com','password'=>'admin123','_csrf_token'=>$csrfToken]);
        $client->followRedirects(false);
        $responseform = $client->submit($form);
        $path = '/en/'.$client->getContainer()->get('router')->generate('index_administrador', [], UrlGeneratorInterface::RELATIVE_PATH);
        $this->assertTrue($client->getResponse()->isRedirect($path),'No hay login hacia el panel del administrador');

        //REQ-PUB-LOG-09: Si el usuario es un comercial, le dirigirá a la página del panel de control del comercial
        $form = $botonsubmit->form(['email'=>'comercial@app.com','password'=>'comercial123','_csrf_token'=>$csrfToken]);
        $client->followRedirects(false);
        $responseform = $client->submit($form);
        $path = '/es/'.$client->getContainer()->get('router')->generate('comercial_presupuesto_index', [], UrlGeneratorInterface::RELATIVE_PATH);
        $this->assertTrue($client->getResponse()->isRedirect($path),'No hay login hacia el panel del comercial');

        //REQ-PUB-LOG-10: Si el usuario es un jefe de proyecto, le dirigirá a la página del panel de control del jefe de proyecto
        $form = $botonsubmit->form(['email'=>'jefeproyecto@app.com','password'=>'jefe123','_csrf_token'=>$csrfToken]);
        $client->followRedirects(false);
        $responseform = $client->submit($form);
        $path = '/en/'.$client->getContainer()->get('router')->generate('jefeproyecto_proyectos_index', [], UrlGeneratorInterface::RELATIVE_PATH);
        $this->assertTrue($client->getResponse()->isRedirect($path),'No hay login hacia el panel del jefeproyecto');

        //REQ-PUB-LOG-11: Si el usuario es un empleado distinto a los enumerados en los requisitos anteriores, le dirigirá a la página del panel de control del empleado.
        $form = $botonsubmit->form(['email'=>'empleado@app.com','password'=>'emp123','_csrf_token'=>$csrfToken]);
        $client->followRedirects(false);
        $responseform = $client->submit($form);
        $path = '/es/'.$client->getContainer()->get('router')->generate('tecnico_proyectos_index', [], UrlGeneratorInterface::RELATIVE_PATH);
        $this->assertTrue($client->getResponse()->isRedirect($path),'No hay login hacia el panel del empleado');
    }
}