Clazz.declarePackage("JM");
Clazz.load(["JM.ProteinStructure"], "JM.Turn", ["J.c.STR"], function(){
var c$ = Clazz.declareType(JM, "Turn", JM.ProteinStructure);
Clazz.makeConstructor(c$,
function(apolymer, monomerIndex, monomerCount){
Clazz.superConstructor (this, JM.Turn, []);
this.setupPS(apolymer, J.c.STR.TURN, monomerIndex, monomerCount);
this.subtype = J.c.STR.TURN;
}, "JM.AlphaPolymer,~N,~N");
});
;//5.0.1-v2 Tue Jul 23 17:25:20 CDT 2024
