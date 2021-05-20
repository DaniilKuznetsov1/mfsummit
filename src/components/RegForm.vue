<template>
  <div class="register">
    <div class="caption">
      Pascal
    </div>
    <div class="container">
      <p class="centercap">Регистрация</p>
      <label class="formcaption">Имя пользователя</label>
      <input type="text" class="editbox" :style="{borderColor: userStyleColor}" v-model="username" @keypress="userKeyPress($event)" @blur="checkUserName" />
      <label class="formcaption hintst" v-show="hintusername != ''">{{ hintusername }}</label>
      <label class="formcaption">Пароль</label>
      <input type="password" class="editbox" v-model="password" />
      <label class="formcaption hintst" v-show="hintpassword != ''">{{ hintpassword }}</label>
      <label class="formcaption">Повторите пароль</label>
      <input type="password" class="editbox" :style="{borderColor: passStyleColor}" v-model="confirmPassword" />
    </div>
    <div class="buttons">
      <button class="button-ok" @click="fregister">Зарегистрироватся</button> <button class="button-cancel">Отмена</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'regform',
  components: {

  },
  data() {
    return {
      username: '',
      password: '',
      confirmPassword: '',
      hintusername: '',
      hintpassword: '',
      userStyleColor: 'gray',
      passStyleColor: 'gray',
    }
  },
  mounted() {
    
  },
  watch: {
    password(newValue) {
      this.password = newValue;
      this.checkLenPassword();
    },
    confirmPassword(newValue) {
      this.confirmPassword = newValue;
      this.checkIdentPass();
    }
  },
  methods: {
    userKeyPress(event) {
      const DelSym = ['/','\\','.',',','?',':','<','>',"'",'"','`','~','&','[',']','{','}','№']
      let key = event.key;
      if (DelSym.indexOf(key) > -1) {
        event.preventDefault();
      } else {
        return true;
      }
    },
    checkUserName() {
      if (this.username.length < 4) {
        this.userStyleColor = 'red';
        this.hintusername = 'Имя пользователя слишком короткое';
      } else {
        this.userStyleColor = 'limegreen';
        this.hintusername = '';
      }
      if (this.username.length > 100) {
        this.userStyleColor = 'red';
        this.hintusername = 'Имя пользователя слишком длинное';
      }
      let postData = {checkUser: this.username};
      fetch('/control.php', {
          method: 'POST',
          credentials: "include",
          body: JSON.stringify(postData),
          headers: { "Content-Type": "application/json"}
        })
        .then((response) => {
          if (response.ok) {
            return response.json();
          } else {
            console.log(response.status + response.statusText);
          }
        })
        .then((data) => {
          if (data.id == -1) {
            this.userStyleColor = 'limegreen';
            this.hintusername = '';
          } else {
            this.userStyleColor = 'red';
            this.hintusername = 'Пользователь уже есть, задайте другое имя';
          }
        });

    },
    checkLenPassword() {
      if (this.password.length < 4) {
        this.hintpassword = 'Пароль слишком короткий';
      } else {
        this.hintpassword = '';
      }
      if (this.password.length > 200) {
        this.hintpassword = 'Пароль слишком длинный';
      }
    },
    checkIdentPass() {
      if (this.password == this.confirmPassword) {
        this.passStyleColor = 'limegreen';
      } else {
        this.passStyleColor = 'red';
      }
    },
    fregister() {
      //fetch
      let postData = {username: this.username, password: this.password, confirmPassword: this.confirmPassword};
      fetch('/control.php', {
          method: 'POST',
          credentials: "include",
          body: JSON.stringify(postData),
          headers: { "Content-Type": "application/json"}
        })
        .then((response) => {
          if (response.ok) {
            return response.json();
          } else {
            console.log(response.status + response.statusText);
          }
        })
        .then((data) => {
          if (data.id > -1) {
            if (data.autorization) {
              //let base_url = window.location.origin;
              //window.location.href = base_url+'/index.php';
              window.location.reload();
            } else {
              this.passStyleColor = 'red';
              this.hintpassword = 'Ошибка авторизации';
            }
          } else {
            this.passStyleColor = 'red';
            this.hintpassword = 'Ошибка создания пользователя';
          }

        });
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .register {
    display: block; box-sizing: border-box; 
    border-radius: 10px; padding: 2rem 2rem 2rem 2rem;
    -webkit-box-shadow: 0px 1px 15px 10px rgba(34, 60, 80, 0.2);
    -moz-box-shadow: 0px 1px 15px 10px rgba(34, 60, 80, 0.2);
    box-shadow: 0px 1px 15px 10px rgba(34, 60, 80, 0.2);
  }
  @media (max-width: 360px) {
    .register {
      width: 95%; height: 95%; margin-top: 5rem;
    }
  }
  @media (max-width: 480px) {
    .register {
      width: 80%; height: 80%; margin-top: 5rem;
    }    
  }
  @media (max-width: 768px) {
    .register {
      width: 70%; height: 70%; margin-top: 8rem;
    }    
  }
  @media (max-width: 1024px) {
    .register {
      width: 50%; height: 50%; margin-top: 8rem;
    } 
  }
  @media (max-width: 1200px) {
    .register {
      width: 50%; height: 50%; margin-top: 10rem;
    } 
  }
  @media (min-width: 1201px) {
    .register {
      width: 40%; height: 40%; margin-top: 10rem;
    } 
  }
  .caption {
    display: block; width: 100%; height: 2rem; box-sizing: border-box;
    font-family: "Roboto"; font-size: 16pt; text-align: center;
  }
  .hintst {
    border-bottom: 1px solid gray; font-size: 0.8rem;
  }
  .centercap {
    font-family: "Roboto"; text-align: center;  padding-top: 2px; padding-bottom: 2px;  margin-top: 2px; margin-bottom: 2px;
  }
  .container {
    display: block; width: 100%; height: calc(100% - 5rem); box-sizing: border-box;
  }
  .buttons {
    display: flex; flex-direction: row; flex-wrap:nowrap; justify-content:flex-end;
    width: 100%; height: 3rem; box-sizing: border-box; padding-top: 1rem;
  }
</style>
