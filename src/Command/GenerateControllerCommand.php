<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateControllerCommand extends Command
{
    protected static $defaultName = 'app:generate-controller';
    private const CONTROLLER_DIR = 'src/Controller/';
    private const TEMPLATE_DIR = 'templates/';

    protected function configure(): void
    {
        $this
            ->setDescription('Generate a custom controller and its corresponding Twig templates')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the controller');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = strtolower($input->getArgument('name'));
        $className = ucfirst($name);
        $controllerFilename = self::CONTROLLER_DIR . $className . 'Controller.php';
        $templateDir = self::TEMPLATE_DIR . $name;
        $nameTemplateFilename = $templateDir . '/' . $name . '.html.twig';
        $indexTemplateFilename = $templateDir . '/index.html.twig';

        $filesystem = new Filesystem();

        // Check if the controller already exists
        if ($filesystem->exists($controllerFilename)) {
            $io->error('Controller already exists.');
            return Command::FAILURE;
        }

        // Generate the controller file
        $controllerTemplate = <<<PHP
<?php

namespace App\Controller;

use App\Entity\\{$className};
use App\Form\\{$className}Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class {$className}Controller extends AbstractController
{
    private EntityManagerInterface \$em;

    public function __construct(EntityManagerInterface \$em)
    {
        \$this->em = \$em;
    }

    #[Route('/{$name}', name: '{$name}_index')]
    public function index(Request \$request, PaginatorInterface \$paginator): Response
    {
        \${$name}s = \$this->em->getRepository({$className}::class)->findAll();
        return \$this->render('{$name}/index.html.twig', [
            '{$name}s' => \${$name}s,
        ]);
    }

    #[Route('/create-{$name}', name: 'create-{$name}')]
    public function create{$className}(Request \$request): Response
    {
        \${$name} = new $className();
        \$form = \$this->createForm({$className}Type::class, \${$name});
        \$form->handleRequest(\$request);

        if (\$form->isSubmitted() && \$form->isValid()) {
            \$this->em->persist(\${$name});
            \$this->em->flush();
            return \$this->redirectToRoute('{$name}_index');
        }

        return \$this->render('{$name}/{$name}.html.twig', [
            'form' => \$form->createView(),
        ]);
    }

    #[Route('/edit-{$name}/{id}', name: 'edit-{$name}')]
    public function edit{$className}(Request \$request, \$id)
    {

        \${$name} = \$this->em->getRepository({$className}::class)->find(\$id);
        \$form = \$this->createForm({$className}Type::class, \${$name});
        \$form->handleRequest(\$request);
        if (\$form->isSubmitted() && \$form->isValid()) {
            \$this->em->persist(\${$name});
            \$this->em->flush();
            return \$this->redirectToRoute('{$name}_index');
        }
        return \$this->render('{$name}/{$name}.html.twig', [
            'form' => \$form->createView(),
        ]);
    }

    #[Route('/delete-{$name}/{id}', name: 'delete-{$name}')]
    public function delete{$className}(int \$id): Response
    {
        \${$name} = \$this->em->getRepository({$className}::class)->find(\$id);

        if (!\${$name}) {
            return new Response('Not found !');
        }

        // foreach (\${$name}->get__() as \$__) {
        //     \$__->set{$className}(null);
        // }

        \$this->em->remove(\${$name});
        \$this->em->flush();
        return \$this->redirectToRoute('{$name}_index');
    }

    #[Route('/search-{$name}s', name: 'search_{$name}s')]
    public function search{$className}s(Request \$request): JsonResponse
    {
        \$searchQuery = \$request->query->get('search_query', '');
        \$searchField = \$request->query->get('search_field', 'name');

        \$queryBuilder = \$this->em->createQueryBuilder();
        \$queryBuilder->select('{$name}')
                     ->from({$className}::class, '{$name}');

        if (!empty(\$searchQuery)) {
            switch (\$searchField) {
                case 'name':
                    \$queryBuilder->andWhere('{$name}.name LIKE :searchQuery')
                                 ->setParameter('searchQuery', '%' . \$searchQuery . '%');
                    break;
            }
        }

        \${$name}s = \$queryBuilder->getQuery()->getResult();

        \$formatted{$className}s = array_map(function ({$className} \${$name}) {
            return [
                'id' => \${$name}->getId(),
                'name' => \${$name}->getName(),
            ];
        }, \${$name}s);

        return \$this->json(\$formatted{$className}s);
    }
}

PHP;

        // Generate the Twig templates
        $nameTemplate = <<<TWIG
{{ form_start(form) }}
{{ form_end(form) }}
TWIG;

        $indexTemplate = <<<TWIG
<a href="/create-{$name}">Thêm</a>
<form id="search-form">
        <label>Tìm kiếm theo:</label>
        <select name="search_field" id="search_field">
            <option value="name">{$className} name</option>
        </select>
        <input type="search" name="search_query" placeholder="Nhập từ khóa...">
        <button type="submit">Tìm kiếm</button>
</form>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="{$name}-table-body">
        {% for {$name} in {$name}s %}
        <tr>
            <td>{{ {$name}.name }}</td>
             {# {% if lop.lop %}
                 <td>{{ lop.lop.name }}</td>
             {% else %}
                 <td></td>
             {% endif %} #}
            <td>
                <a href="/edit-{$name}/{{ {$name}.id }}">Sửa</a>
                |
                <a href="/delete-{$name}/{{ {$name}.id }}">Xóa</a>
            </td>
        </tr>
        {% endfor %}
    </tbody>
</table>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchForm = document.querySelector('#search-form');
        searchForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const {$name}TableBody = document.querySelector('#{$name}-table-body');
            {$name}TableBody.innerHTML = '';
            const searchField = document.querySelector('select[name="search_field"]').value;
            const searchQuery = document.querySelector('input[name="search_query"]').value;
            fetch(`/search-{$name}s?search_field=\${searchField}&search_query=\${encodeURIComponent(searchQuery)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        {$name}TableBody.innerHTML = '<tr><td colspan="5">Not found !</td></tr>';
                    } else {
                        data.forEach(({$name}, index) => {
                            const row = `
                                <tr>
                                    <td>\${{$name}.name}</td>
                                    <td>
                                        <a href="/edit-{$name}/\${{$name}.id}">Sửa</a>
                                        |
                                        <a href="/delete-{$name}/\${{$name}.id}">Xóa</a>
                                    </td>
                                </tr>`;
                            {$name}TableBody.innerHTML += row;
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    });
</script>
TWIG;

        try {
            // Create the controller file
            $filesystem->dumpFile($controllerFilename, $controllerTemplate);

            // Create the template directory if it doesn't exist
            if (!$filesystem->exists($templateDir)) {
                $filesystem->mkdir($templateDir);
            }

            // Create the Twig templates
            $filesystem->dumpFile($nameTemplateFilename, $nameTemplate);
            $filesystem->dumpFile($indexTemplateFilename, $indexTemplate);

            $io->success("Controller {$className}Controller.php and Twig templates created successfully!");
        } catch (\Exception $e) {
            $io->error('An error occurred while generating files: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
