import React from 'react';
import ReactDOM from 'react-dom';
import Button from 'react-bootstrap/lib/Button';
import ButtonTollbar from 'react-bootstrap/lib/ButtonToolbar';
import FormGroup from 'react-bootstrap/lib/FormGroup';
import FormControl from 'react-bootstrap/lib/FormControl';
import ControlLabel from 'react-bootstrap/lib/ControlLabel';

class TopHint extends React.Component {
  constructor(props) {
    super(props);
  }
  render() {
    return (<h1>Hello, world! {this.props.userName}</h1>);
  }
}

class FormExample extends React.Component {
  constructor(props) {
    super(props);
  }
  render() {
    return (
      <form>
        <FormGroup controlId="formBasicText">
          <ControlLabel>Name Input</ControlLabel>
          <FormControl type="text" value={this.props.userName} placeholder="Enter name" onChange={this.props.onNameChange}></FormControl>
        </FormGroup>
      </form>
    );
  }
}

class App extends React.Component {
  constructor() {
    super();
    this.state = {userName: "wander"};
    //this.handleNameChange = this.handleNameChange.bind(this);
  }
  
  handleNameChange(e) {
    const newname = e.target.value;
    console.log(newname);
    //this.state.name = newname;
    this.setState({userName : newname});
  }
  render() {
    return (
      <div>
        <TopHint userName={this.state.userName}/>
        <div style={{ width : '50%'}}>
          <FormExample onNameChange={this.handleNameChange.bind(this)} userName={this.state.userName}/>
        </div>
        <div>
          <ButtonTollbar>
            <Button>Default</Button>
            <Button bsStyle="success">Success</Button>
          </ButtonTollbar>
        </div>
      </div>
    );
  }
}

ReactDOM.render(
  <App />,
  document.getElementById('app')
);