import logging
import aiohttp
from aiogram import Bot, Dispatcher
from aiogram.filters import Command
from aiogram.types import Message
import asyncio

API_TOKEN = '7075576256:AAHoiXya44fdOgzqzsTBCWaqe54a6oq6USY'

# логирование
logging.basicConfig(level=logging.INFO)

# объекты Bot и Dispatcher
bot = Bot(token=API_TOKEN)
dp = Dispatcher()

# Базовое исключение для всех ошибок библиотеки
class LibraryError(Exception):
    pass

# Исключение для случая, когда книга не найдена
class BookNotFoundError(LibraryError):
    pass

# Исключение для случая, когда сервер недоступен
class ServerError(LibraryError):
    pass

# Функция для поиска книги
async def search_book(author: str, title: str):
    search_url = f"https://openlibrary.org/search.json?author={author}&title={title}"
    async with aiohttp.ClientSession() as session:
        async with session.get(search_url) as response:
            if response.status == 200:
                data = await response.json()
                if data['numFound'] > 0:
                    book_info = data['docs'][0]
                    book_title = book_info.get('title', 'Неизвестно')
                    book_author = ', '.join(book_info.get('author_name', ['Неизвестно']))
                    book_link = f"https://openlibrary.org{book_info.get('key', '')}"
                    return f"Название: {book_title}\nАвтор: {book_author}\nСсылка: {book_link}"
                else:
                    raise BookNotFoundError("Книга не найдена")
            else:
                raise ServerError("Ошибка при запросе к серверу")

# командf /start
@dp.message(Command("start"))
async def cmd_start(message: Message):
    await message.answer("Привет! Я бот для поиска электронных версий книг. Отправь мне автора и название книги в формате:\n\nАвтор: Название")

# Обработчик сообщений с информацией о книге
@dp.message()
async def get_book_info(message: Message):
    try:
        author, title = message.text.split(':', 1)
        author = author.strip()
        title = title.strip()
        result = await search_book(author, title)
        await message.answer(result)
    except ValueError:
        await message.answer("Пожалуйста, отправь информацию в формате: Автор: Название")
    except BookNotFoundError as BNFE:
        await message.answer(str(BNFE))
    except ServerError as SE:
        await message.answer(str(SE))
    except LibraryError as LE:
        await message.answer(f"Произошла ошибка: {LE}")

async def on_startup(bot: Bot):
    await bot.delete_webhook(drop_pending_updates=True)

async def main():
    await on_startup(bot)
    await dp.start_polling(bot)

if __name__ == "__main__":
    asyncio.run(main())