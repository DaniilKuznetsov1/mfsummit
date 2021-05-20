<template>
  <div class="root">
    <div class="header">
      <div class="link" v-on:click="fregister">Регистрация</div> <div class="link" v-on:click="flogin">Вход</div> 
    </div>
    <div class="content">
        <loginform v-if="register == false" />
        <regform  v-if="register == true"/>
    </div>
    <div class="footer">
      
    </div>
  </div>
</template>

<script>
import loginform from './components/login.vue'
import regform from './components/RegForm.vue'

export default {
  name: 'App',
  components: {
    loginform,
    regform
  },
  data() {
    return {
      register: false,
    }
  },
  mounted() {
    
  },
  methods: {
    flogin() {
      this.register = false;
    },
    fregister() {
      this.register = true;
    }
  }
}
</script>

<style>
@font-face {
  font-family: "Roboto";
  font-weight: 400;
  font-style: normal;
  font-display: auto;
  src: local("Roboto"), url("./fonts/Roboto-Regular.ttf") format("truetype");
}
@font-face {
  font-family: "Nunito";
  font-weight: 400;
  font-style: normal;
  font-display: auto;
  src: local("Nunito"), url("./fonts/Nunito-Regular.ttf") format("truetype");
}

html, body {
  width: 100%; height: 100%; box-sizing: border-box;
  margin: 0px 0px 0px 0px;
}
#app {
  width: 100%; height: 100%; box-sizing: border-box;
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}
.root {
  display: block;
  width: 100%; height: 100%; box-sizing: border-box;
  padding: 3px 3px 3px 3px;
}
.header {
  display: flex; flex-direction: row; flex-wrap:nowrap; justify-content:flex-end;
  width: 100%; height: 2rem; box-sizing: border-box; 
}
.content {
  display: flex; flex-direction: row; justify-content:center;
  width: 100%; height: calc(100% - 4rem); box-sizing: border-box;
}
.footer {
  display: block; width: 100%; height: 2rem; box-sizing: content-box;
}

.link {
  margin-left: 4px; margin-right: 4px; color: black;
  font-weight: bold; text-decoration: none; cursor: pointer;
}
.link:hover {
  text-decoration: underline; color: blue; cursor: hand;
}

.formcaption {
  display: block; width: 100%; font-family: "Nunito"; text-align:left;
}

.editbox {
  display: block; width: 100%; border-width: 1px; border-style: solid; border-radius: 5px; border-color: silver; 
  padding: 5px 5px 5px 5px;
}
.editbox:hover {
  display: block; width: 100%; border-width: 2px; border-style: solid; border-radius: 5px; border-color: gray; 
}

.button-ok {
  border: 1px solid black; border-radius: 5px; background-color: black; color: white; font-weight: normal;
  margin-left: 4px; margin-right: 4px; width: 10rem;
} 
.button-ok:hover {
  border: 2px solid black; border-radius: 5px; background-color: black; color: white; font-weight: bold;
}
.button-ok:active {
  border: 2px solid black; border-radius: 5px; background-color: blue; color: white; font-weight: bold;
}
.button-ok:disabled {
  border: 1px solid black; border-radius: 5px; background-color: black; color: gray; font-weight: normal;
}

.button-cancel {
  border: 1px solid silver; border-radius: 5px; background-color: white; font-weight: normal;
  margin-left: 4px; margin-right: 4px; width: 10rem;
}
.button-cancel:hover {
  border: 2px solid silver; border-radius: 5px; background-color: white; font-weight: bold;
}
.button-cancel:active {
  border: 1px solid gray; border-radius: 5px; background-color: silver; font-weight: bold;
}
.button-cancel:disabled {
  border: 1px solid silver; border-radius: 5px; background-color:whitesmoke; font-weight: normal; color: silver;
}
</style>
