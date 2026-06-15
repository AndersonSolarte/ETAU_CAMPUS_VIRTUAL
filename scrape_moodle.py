import requests
from bs4 import BeautifulSoup

def login():
    session = requests.Session()
    login_url = "https://virtual.umariana.edu.co/campus/login/index.php"
    
    # 1. Get the login page to grab the logintoken
    r = session.get(login_url)
    soup = BeautifulSoup(r.text, 'html.parser')
    logintoken_input = soup.find('input', {'name': 'logintoken'})
    
    if not logintoken_input:
        print("Could not find logintoken")
        return None

    logintoken = logintoken_input['value']
    print(f"Found logintoken: {logintoken}")
    
    # 2. Login
    payload = {
        'username': 'andsolarte126@umariana.edu.co',
        'password': '1085327166Ander*',
        'logintoken': logintoken
    }
    
    r_login = session.post(login_url, data=payload)
    if "andsolarte126" in r_login.text or "Salir" in r_login.text or "Logout" in r_login.text or "Mis cursos" in r_login.text:
        print("Logged in successfully!")
    else:
        print("Login might have failed")

    # 3. Go to courses page
    courses_url = "https://virtual.umariana.edu.co/campus/my/courses.php"
    r_courses = session.get(courses_url)
    
    with open("courses_page.html", "w", encoding="utf-8") as f:
        f.write(r_courses.text)
        
    print("Saved courses_page.html")

if __name__ == "__main__":
    login()
