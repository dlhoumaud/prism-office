<?php

declare(strict_types=1);

namespace PrismOffice\Tests\Infrastructure\FileSystem;

use PHPUnit\Framework\TestCase;
use PrismOffice\Infrastructure\FileSystem\NativeFileSystem;

final class NativeFileSystemTest extends TestCase
{
    private NativeFileSystem $fileSystem;
    private string $testDir;

    protected function setUp(): void
    {
        $this->fileSystem = new NativeFileSystem();
        $this->testDir = sys_get_temp_dir() . '/prism_test_' . uniqid();
        mkdir($this->testDir, 0755, true);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->testDir)) {
            $this->removeDirectory($this->testDir);
        }
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = scandir($dir);
        if ($items === false) {
            return;
        }
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($dir);
    }

    public function testScanDirectory(): void
    {
        file_put_contents($this->testDir . '/file1.txt', 'content');
        file_put_contents($this->testDir . '/file2.txt', 'content');
        mkdir($this->testDir . '/subdir');

        $result = $this->fileSystem->scanDirectory($this->testDir);

        $this->assertIsArray($result);
        $this->assertContains('file1.txt', $result);
        $this->assertContains('file2.txt', $result);
        $this->assertContains('subdir', $result);
    }

    public function testScanDirectoryReturnsFalseForNonExistentDirectory(): void
    {
        $result = $this->fileSystem->scanDirectory('/non/existent/path');

        $this->assertFalse($result);
    }

    public function testIsDirectory(): void
    {
        $this->assertTrue($this->fileSystem->isDirectory($this->testDir));
        $this->assertFalse($this->fileSystem->isDirectory($this->testDir . '/non_existent'));
    }

    public function testIsFile(): void
    {
        $filePath = $this->testDir . '/test.txt';
        file_put_contents($filePath, 'content');

        $this->assertTrue($this->fileSystem->isFile($filePath));
        $this->assertFalse($this->fileSystem->isFile($this->testDir));
    }

    public function testFileExists(): void
    {
        $filePath = $this->testDir . '/test.txt';
        file_put_contents($filePath, 'content');

        $this->assertTrue($this->fileSystem->fileExists($filePath));
        $this->assertTrue($this->fileSystem->fileExists($this->testDir));
        $this->assertFalse($this->fileSystem->fileExists('/non/existent/path'));
    }

    public function testReadFile(): void
    {
        $filePath = $this->testDir . '/test.txt';
        $content = 'Test content';
        file_put_contents($filePath, $content);

        $result = $this->fileSystem->readFile($filePath);

        $this->assertSame($content, $result);
    }

    public function testReadFileReturnsFalseForNonExistentFile(): void
    {
        $result = $this->fileSystem->readFile('/non/existent/file.txt');

        $this->assertFalse($result);
    }

    public function testWriteFile(): void
    {
        $filePath = $this->testDir . '/test.txt';
        $content = 'Test content';

        $result = $this->fileSystem->writeFile($filePath, $content);

        $this->assertSame(strlen($content), $result);
        $this->assertSame($content, file_get_contents($filePath));
    }

    public function testCreateDirectory(): void
    {
        $dirPath = $this->testDir . '/new_dir';

        $result = $this->fileSystem->createDirectory($dirPath);

        $this->assertTrue($result);
        $this->assertTrue(is_dir($dirPath));
    }

    public function testCreateDirectoryRecursive(): void
    {
        $dirPath = $this->testDir . '/parent/child/grandchild';

        $result = $this->fileSystem->createDirectory($dirPath, 0755, true);

        $this->assertTrue($result);
        $this->assertTrue(is_dir($dirPath));
    }

    public function testDeleteFile(): void
    {
        $filePath = $this->testDir . '/test.txt';
        file_put_contents($filePath, 'content');

        $result = $this->fileSystem->deleteFile($filePath);

        $this->assertTrue($result);
        $this->assertFalse(file_exists($filePath));
    }

    public function testDeleteFileReturnsFalseForNonExistentFile(): void
    {
        $result = $this->fileSystem->deleteFile('/non/existent/file.txt');

        $this->assertFalse($result);
    }

    public function testDeleteDirectory(): void
    {
        $dirPath = $this->testDir . '/empty_dir';
        mkdir($dirPath);

        $result = $this->fileSystem->deleteDirectory($dirPath);

        $this->assertTrue($result);
        $this->assertFalse(is_dir($dirPath));
    }

    public function testDeleteDirectoryReturnsFalseForNonExistentDirectory(): void
    {
        $result = $this->fileSystem->deleteDirectory('/non/existent/directory');

        $this->assertFalse($result);
    }

    public function testGetRealPath(): void
    {
        $result = $this->fileSystem->getRealPath($this->testDir);

        $this->assertIsString($result);
        $this->assertNotFalse($result);
    }

    public function testGetRealPathReturnsFalseForNonExistentPath(): void
    {
        $result = $this->fileSystem->getRealPath('/non/existent/path');

        $this->assertFalse($result);
    }

    public function testGetDirectoryName(): void
    {
        $filePath = '/path/to/file.txt';

        $result = $this->fileSystem->getDirectoryName($filePath);

        $this->assertSame('/path/to', $result);
    }
}
